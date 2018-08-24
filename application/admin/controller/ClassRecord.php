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
use app\admin\model\ClassRecord as ClassRecordModel;
use app\admin\model\ClassHour;
use app\admin\model\CourseUser;
use think\Config;

/**
 * Class ClassRecord
 * @package  课时成员控制器
 */
class ClassRecord extends Admin {
    /**
     * 主页列表
     */
    public function index(){
        $type = input('type');
        $date = input('date');
        $venue = input('venue');
        $course = input('course');
        $pid = input('pid');
        $map = array(
            'class_id' => $pid,
            'status' => array('egt',0),
        );
        $list = $this->lists('ClassRecord',$map);
        int_to_string($list,array(
            'member_type' => array(1 =>"教练", 2 =>"学员", 3 =>"代课教练"),
            'status' => array(0 =>"已发布"),
        ));
        $name = ClassHour::where('id', $pid)->value('class_name');

        $this->assign('list',$list);
        $this->assign('pid',$pid);
        $this->assign('name',$name);
        $this->assign('date',$date);
        $this->assign('type',$type);
        $this->assign('venue',$venue);
        $this->assign('course',$course);

        return $this->fetch();
    }

    /**
     * 添加
     */
    public function add(){
        $type = input('type');
        $date = input('date');
        $venue = input('venue');
        $course = input('course');
        if(IS_POST) {
            $data = input('post.');
            if(empty($data['id'])){
                unset($data['id']);
            }
            $classHourModel = ClassHour::get($data['class_id']);
            $data['name'] = WechatUser::where(['userid' => $data['userid']])->value('name');
            $data['venue_id'] = $classHourModel['venue_id'];
            $data['course_id'] = $classHourModel['course_id'];
            $data['openid'] = WechatUser::where(['userid' => $data['userid']])->value('openid');
            $data['date'] = date('Y-m-d', $classHourModel['start_time']);
            $data['start_time'] = $classHourModel['start_time'];
            $data['end_time'] = $classHourModel['end_time'];

            if(!$data['openid']){
                return $this->error('该成员还未关注企业号！');
            }

            $model = ClassRecordModel::where(['class_id' => $data['class_id'], 'userid' => $data['userid'], 'status' => 0])->find();
            if($model){
                return $this->error('请不要重复添加！');
            }

            $classRecordModel = new ClassRecordModel();
            $info = $classRecordModel->validate('ClassRecord')->save($data);
            if($info) {
                return $this->success("添加成功", Url('ClassRecord/index', array('pid' => $data['class_id'], 'type' => $type, 'date' => $date, 'venue' => $venue, 'course' => $course)));
            }else{
                return $this->error($classRecordModel->getError());
            }
        }else{
            $pid = input('pid');
            $model = ClassHour::get($pid);
            $userList = WechatUserTag::where('tagid', ['=', WechatTag::TAG_2], ['=', WechatTag::TAG_3], ['=', WechatTag::TAG_4], ['=', WechatTag::TAG_5], ['=', WechatTag::TAG_6], ['=', WechatTag::TAG_7], 'or')->field('userid')->select();
            $coachList = CourseUser::where(['course_id' => $model['course_id'], 'member_type' => 1, 'status' => 0])->field('userid, name')->select();
            foreach ($userList as $key => $val) {
                $userList[$key]['name'] = WechatUser::where(['userid' => $val['userid']])->value('name');
            }
            $this->assign('msg','');
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
            $classRecordModel = new ClassRecordModel();
            $info = $classRecordModel->validate('ClassRecord')->save($data,['id'=>input('id')]);
            if($info){
                return $this->success("修改成功",Url("ClassRecord/index", array('pid' => $data['class_id'])));
            }else{
                return $this->get_update_error_msg($classRecordModel->getError());
            }
        }else{
            $id = input('id');
            $pid = input('pid');
            $msg = ClassRecordModel::get($id);
            $model = ClassHour::get($pid);
            $this->assign('msg',$msg);
            $this->assign('pid',$pid);
            $this->assign('model',$model);

            return $this->fetch();
        }
    }

    /**
     * 删除
     */
    public function del(){
        $id = input('id');
        $data['status'] = '-1';
        $info = ClassRecordModel::where('id',$id)->update($data);
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
        $info = ClassRecordModel::where('id', 'in', $ids)->update($data);

        if ($info) {
            return $this->success('批量删除成功');
        } else {
            return $this->error('批量删除失败');
        }
    }
}