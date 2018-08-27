<?php
/**
 * Created by PhpStorm.
 * User: 王泽锋
 * Date: 2018/07/17
 * Time: 09:28
 */
namespace app\admin\controller;

use think\Controller;
use app\admin\model\Picture;
use app\admin\model\Push;
use com\wechat\TPQYWechat;
use app\admin\model\Course as CourseModel;
use app\admin\model\Venue as VenueModel;
use app\admin\model\VenueCourse;
use app\admin\model\CourseApply;
use app\admin\model\CourseUser;
use think\Config;

/**
 * Class Course
 * @package  签到课程管理控制器
 */
class Course extends Admin {
    /**
     * 主页列表
     */
    public function index(){
        $map = array(
            'status' => array('egt',0),
        );
        $search = input('search');
        if ($search != '') {
            $map['course_name'] = ['like', '%' . $search . '%'];
        }
        $list = $this->lists('Course',$map);
        $venue = VenueModel::where(['status' => ['>=', 0]])->column('id,title');
        int_to_string($list,array(
            'venue_id' => $venue,
            'type' => array(1 =>"普通课",2=>"精品课"),
            'status' => array(0 =>"已发布",1=>"已推送"),
        ));

        $this->assign('venue',$venue);
        $this->assign('list',$list);

        return $this->fetch();
    }

    /**
     * 添加
     */
    public function add(){
        if(IS_POST) {
            $data = input('post.');
            if($data['end_time'] < $data['start_time']){
                return $this->error("课程结束时间必须大于开始时间");
            }
            $data['create_user'] = $_SESSION['think']['user_auth']['id'];
            if(empty($data['id'])){
                unset($data['id']);
            }
            if($data['start_time']){
                $data['start_time'] = strtotime($data['start_time']);
            }
            if($data['end_time']){
                $data['end_time'] = strtotime($data['end_time']);
            }

            $courseModel = new CourseModel();
            $info = $courseModel->validate('Course')->save($data);
            if($info) {
                //自动添加报名的学员
                $rs = CourseApply::where(['venue_id' => $data['venue_id'], 'course_id' => $data['pid'], 'status' => 1])->select();
                foreach ($rs as $val) {
                    $param['course_id'] = $courseModel->id;
                    $param['userid'] = $val['userid'];
                    $param['member_type'] = 2;
                    $param['name'] = $val['name'];
                    $param['num'] = $data['num'];
                    $param['start_time'] = $data['start_time'];
                    $param['end_time'] = $data['end_time'];
                    $res = CourseUser::where($param)->find();
                    if ($res) {
                        CourseUser::where(['id'=>$res['id']])->update(['status'=>0]);
                    } else {
                        CourseUser::create($param);
                    }
                }
                return $this->success("添加成功",Url('Course/index'));
            }else{
                return $this->error($courseModel->getError());
            }
        }else{
            $venue = VenueModel::where(['status' => ['>=', 0]])->column('id,title');
            $list = VenueCourse::where(['status' => ['>=', 0]])->column('id,course_name');
            $this->assign('list',$list);
            $this->assign('venue',$venue);
            $this->assign('msg','');

            return $this->fetch('edit');
        }
    }


    /**
     * 根据场馆选择课程
     */
    public function getCourse(){
        $id = input('id');
        $list = VenueCourse::where(['venue_id' => $id, 'status' => ['>=', 0]])->column('id,course_name');

        return $this->success("成功",'',$list);
    }
    /**
     * 修改
     */
    public function edit(){
        if(IS_POST) {
            $data = input('post.');
            if($data['end_time'] < $data['start_time']){
                return $this->error("课程结束时间必须大于开始时间");
            }
            if($data['start_time']){
                $data['start_time'] = strtotime($data['start_time']);
            }
            if($data['end_time']){
                $data['end_time'] = strtotime($data['end_time']);
            }
            $courseModel = new CourseModel();
            $info = $courseModel->validate('Course')->save($data,['id'=>input('id')]);
            if($info){
                //自动修改课程下的学员
                CourseUser::where('course_id',input('id'))->update(['num' => $data['num'], 'start_time' => $data['start_time'], 'end_time' => $data['end_time']]);
                return $this->success("修改成功",Url("Course/index"));
            }else{
                return $this->get_update_error_msg($courseModel->getError());
            }
        }else{
            $id = input('id');
            $venue = VenueModel::where(['status' => ['>=', 0]])->column('id,title');
            $list = VenueCourse::where(['status' => ['>=', 0]])->column('id,course_name');
            $msg = CourseModel::get($id);
            $this->assign('list',$list);
            $this->assign('venue',$venue);
            $this->assign('msg',$msg);

            return $this->fetch();
        }
    }

    /**
     * 删除
     */
    public function del(){
        $id = input('id');
        $data['status'] = '-1';
        $info = CourseModel::where('id',$id)->update($data);
        if($info) {
            CourseUser::where('course_id',$id)->update($data);
            return $this->success("删除成功");
        }else{
            return $this->error("删除失败");
        }

    }

    /**
     * 批量删除
     */
    public function moveToTrash()
    {
        $ids = input('ids/a');
        if (!$ids) {
            return $this->error('请勾选删除选项');
        }
        $data['status'] = '-1';
        $info = CourseModel::where('id', 'in', $ids)->update($data);

        if ($info) {
            CourseUser::where('course_id', 'in', $ids)->update($data);
            return $this->success('批量删除成功');
        } else {
            return $this->error('批量删除失败');
        }
    }
}