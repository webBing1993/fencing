<?php
/**
 * Created by PhpStorm.
 * User: 王泽锋
 * Date: 2018/07/17
 * Time: 09:28
 */
namespace app\admin\controller;

use app\admin\model\WechatTag;
use app\admin\model\WechatUser;
use app\home\model\WechatUserTag;
use think\Controller;
use app\admin\model\Picture;
use app\admin\model\Push;
use com\wechat\TPQYWechat;
use app\admin\model\CourseUser as CourseUserModel;
use app\admin\model\Course as CourseModel;
use think\Config;

/**
 * Class CourseUser
 * @package  课程成员控制器
 */
class CourseUser extends Admin {
    /**
     * 主页列表
     */
    public function index(){
        $pid = input('pid');
        $map = array(
            'course_id' => $pid,
            'status' => array('egt',0),
        );
        $list = $this->lists('CourseUser',$map);
        int_to_string($list,array(
            'member_type' => array(1 =>"教练", 2 =>"学员"),
            'status' => array(0 =>"已发布"),
        ));
        $name = CourseModel::where('id', $pid)->value('course_name');

        $this->assign('list',$list);
        $this->assign('pid',$pid);
        $this->assign('name',$name);

        return $this->fetch();
    }

    /**
     * 添加
     */
    public function add(){
        if(IS_POST) {
            $data = input('post.');
            if(empty($data['id'])){
                unset($data['id']);
            }
            $courseModel = CourseModel::get($data['course_id']);
            $data['name'] = WechatUser::where(['userid' => $data['userid']])->value('name');
            $data['num'] = $courseModel['num'];
            $data['start_time'] = $courseModel['start_time'];
            $data['end_time'] = $courseModel['end_time'];
            $courseUserModel = new CourseUserModel();
            $info = $courseUserModel->validate('CourseUser')->save($data);
            if($info) {
                return $this->success("添加成功", Url('CourseUser/index', array('pid' => $data['course_id'])));
            }else{
                return $this->error($courseUserModel->getError());
            }
        }else{
            $pid = input('pid');
            $this->assign('msg','');
            $model = CourseModel::get($pid);
            $userList = WechatUserTag::where('tagid', WechatTag::TAG_8)->field('userid')->select();
            $coachList = WechatUserTag::where('tagid', ['=', WechatTag::TAG_5], ['=', WechatTag::TAG_6], 'or')->field('userid')->select();
            foreach ($userList as $key => $val) {
                $userList[$key]['name'] = WechatUser::where(['userid' => $val['userid']])->value('name');
            }
            foreach ($coachList as $key => $val) {
                $coachList[$key]['name'] = WechatUser::where(['userid' => $val['userid']])->value('name');
            }
            $this->assign('pid',$pid);
            $this->assign('model',$model);
            $this->assign('userList',$userList);
            $this->assign('coachList',$coachList);

            return $this->fetch('edit');
        }
    }

    /**
     * 修改
     */
    public function edit(){
        if(IS_POST) {
            $data = input('post.');
            if($data['end_time'] < $data['start_time']){
                return $this->error("结束时间必须大于开始时间");
            }
            if($data['start_time']){
                $data['start_time'] = strtotime($data['start_time']);
            }
            if($data['end_time']){
                $data['end_time'] = strtotime($data['end_time']);
            }
            $courseUserModel = new CourseUserModel();
            $info = $courseUserModel->validate('CourseUser')->save($data,['id'=>input('id')]);
            if($info){
                return $this->success("修改成功",Url("CourseUser/index", array('pid' => $data['course_id'])));
            }else{
                return $this->get_update_error_msg($courseUserModel->getError());
            }
        }else{
            $pid = input('pid');
            $id = input('id');
            $msg = CourseUserModel::get($id);
            $model = CourseModel::get($pid);
            $this->assign('pid',$pid);
            $this->assign('model',$model);
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
        $info = CourseUserModel::where('id',$id)->update($data);
        if($info) {
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
        $info = CourseUserModel::where('id', 'in', $ids)->update($data);

        if ($info) {
            return $this->success('批量删除成功', url('index'));
        } else {
            return $this->error('批量删除失败');
        }
    }
}