<?php
/**
 * Created by PhpStorm.
 * User: 王泽锋
 * Date: 2018/07/17
 * Time: 09:28
 */
namespace app\admin\controller;

use app\admin\model\ClassRecord;
use app\admin\model\WechatUser;
use think\Controller;
use app\admin\model\Picture;
use app\admin\model\Push;
use com\wechat\TPQYWechat;
use app\admin\model\ClassHour as ClassHourModel;
use app\admin\model\Venue as VenueModel;
use app\admin\model\Course;
use app\admin\model\VenueCourse;
use app\admin\model\CourseUser;
use think\Config;

/**
 * Class ClassHour
 * @package  签到课时管理控制器
 */
class ClassHour extends Admin {
    /**
     * 主页列表
     */
    public function index(){
        $date = input('date');
        $type = input('type');
        $venue = input('venue');
        $course = input('course');
        $map = array(
            'venue_id' => $venue,
            'course_id' => $course,
            'status' => array('egt',0),
        );
        $search = input('search');
        if ($search != '') {
            $map['class_name'] = ['like', '%' . $search . '%'];
        }
        $list = $this->lists('ClassHour',$map);
        $venue_name = VenueModel::get($venue);
        $course_name = Course::get($course);
        int_to_string($list,array(
            'venue_id' => array($venue => $venue_name['title']),
            'course_id' => array($course => $course_name['course_name']),
            'status' => array(0 =>"已发布"),
        ));

        $this->assign('date',$date);
        $this->assign('type',$type);
        $this->assign('venue',$venue);
        $this->assign('course',$course);
        $this->assign('list',$list);

        return $this->fetch();
    }

    /**
     * 添加
     */
    public function add(){
        $type = input('type');
        $date = input('date');
        $venue_id = input('venue');
        $course_id = input('course');
        if(IS_POST) {
            $data = input('post.');
            if($data['end_time'] < $data['start_time']){
                return $this->error("课时结束时间必须大于开始时间");
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

            $classHourModel = new ClassHourModel();
            $info = $classHourModel->validate('ClassHour')->save($data);
            if($info) {
                //自动添加课程里的学员
                $rs = CourseUser::where(['course_id' => $data['course_id'], 'member_type' => 2, 'status' => 0])->select();
                foreach ($rs as $val) {
                    $param['venue_id'] = $data['venue_id'];
                    $param['course_id'] = $data['course_id'];
                    $param['class_id'] = $classHourModel->id;
                    $param['userid'] = $val['userid'];
                    $param['openid'] = WechatUser::where(['userid' => $val['userid']])->value('openid');
                    $param['member_type'] = 2;
                    $param['name'] = $val['name'];
                    $param['date'] = $date;
                    $param['start_time'] = $data['start_time'];
                    $param['end_time'] = $data['end_time'];
                    $res = ClassRecord::where($param)->find();
                    if ($res) {
                        ClassRecord::where(['id'=>$res['id']])->update(['status'=>0]);
                    } else {
                        ClassRecord::create($param);
                    }
                }
                return $this->success("添加成功",Url('ClassHour/index', array('type' => $type, 'date' => $date, 'venue' => $venue_id, 'course' => $course_id)));
            }else{
                return $this->error($classHourModel->getError());
            }
        }else{
            $venue = VenueModel::where(['id' => $venue_id])->field('id,title')->find();
            $course = VenueCourse::where(['id' => $course_id])->field('id,course_name')->find();
            $arr = [
                1 => [
                    'start' => '09:00',
                    'end' => '12:00',
                ],
                2 => [
                    'start' => '13:00',
                    'end' => '17:00',
                ],
                3 => [
                    'start' => '19:00',
                    'end' => '21:00',
                ],
            ];
            $start = $date.' '.$arr[$type]['start'];
            $end = $date.' '.$arr[$type]['end'];
            $this->assign('course',$course);
            $this->assign('venue',$venue);
            $this->assign('start',$start);
            $this->assign('end',$end);
            $this->assign('msg','');

            return $this->fetch('edit');
        }
    }

    /**
     * 修改
     */
    public function edit(){
        $type = input('type');
        $date = input('date');
        $venue_id = input('venue');
        $course_id = input('course');
        if(IS_POST) {
            $data = input('post.');
            if($data['end_time'] < $data['start_time']){
                return $this->error("课时结束时间必须大于开始时间");
            }
            if($data['start_time']){
                $data['start_time'] = strtotime($data['start_time']);
            }
            if($data['end_time']){
                $data['end_time'] = strtotime($data['end_time']);
            }
            $classHourModel = new ClassHourModel();
            $info = $classHourModel->validate('ClassHour')->save($data,['id'=>input('id')]);
            if($info){
                //自动修改课时下的学员
                ClassRecord::where('class_id',input('id'))->update(['num' => $data['num'], 'start_time' => $data['start_time'], 'end_time' => $data['end_time']]);
                return $this->success("修改成功",Url('ClassHour/index', array('type' => $type, 'date' => $date, 'venue' => $venue_id, 'course' => $course_id)));
            }else{
                return $this->get_update_error_msg($classHourModel->getError());
            }
        }else{
            $id = input('id');
            $msg = ClassHourModel::get($id);
            $venue = VenueModel::where(['id' => $venue_id])->field('id,title')->find();
            $course = VenueCourse::where(['id' => $course_id])->field('id,course_name')->find();
            $arr = [
                1 => [
                    'start' => '09:00',
                    'end' => '12:00',
                ],
                2 => [
                    'start' => '13:00',
                    'end' => '17:00',
                ],
                3 => [
                    'start' => '19:00',
                    'end' => '21:00',
                ],
            ];
            $start = $date.' '.$arr[$type]['start'];
            $end = $date.' '.$arr[$type]['end'];
            $this->assign('course',$course);
            $this->assign('venue',$venue);
            $this->assign('start',$start);
            $this->assign('end',$end);
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
        $info = ClassHourModel::where('id',$id)->update($data);
        if($info) {
            ClassRecord::where('class_id',$id)->update($data);
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
        $info = ClassHourModel::where('id', 'in', $ids)->update($data);

        if ($info) {
            ClassRecord::where('class_id', 'in', $ids)->update($data);
            return $this->success('批量删除成功');
        } else {
            return $this->error('批量删除失败');
        }
    }
}