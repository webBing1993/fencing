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
use app\admin\model\VenueCourse as VenueCourseModel;
use app\admin\model\Venue as VenueModel;
use think\Config;

/**
 * Class VenueCourse
 * @package  课程管理控制器
 */
class VenueCourse extends Admin {
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
        $list = $this->lists('VenueCourse',$map);
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
            $venueCourseModel = new VenueCourseModel();
            $info = $venueCourseModel->validate('VenueCourse')->save($data);
            if($info) {
                return $this->success("添加成功",Url('VenueCourse/index'));
            }else{
                return $this->error($venueCourseModel->getError());
            }
        }else{
            $venue = VenueModel::where(['status' => ['>=', 0]])->column('id,title');

            $this->assign('venue',$venue);
            $this->assign('msg','');

            return $this->fetch('edit');
        }
    }

    /**
     * 修改
     */
    public function edit(){
        if(IS_POST) {
            $data = input('post.');
            if($data['start_time']){
                $data['start_time'] = strtotime($data['start_time']);
            }
            if($data['end_time']){
                $data['end_time'] = strtotime($data['end_time']);
            }
            $venueCourseModel = new VenueCourseModel();
            $info = $venueCourseModel->validate('VenueCourse')->save($data,['id'=>input('id')]);
            if($info){
                return $this->success("修改成功",Url("VenueCourse/index"));
            }else{
                return $this->get_update_error_msg($venueCourseModel->getError());
            }
        }else{
            $id = input('id');
            $venue = VenueModel::where(['status' => ['>=', 0]])->column('id,title');

            $msg = VenueCourseModel::get($id);
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
        $info = VenueCourseModel::where('id',$id)->update($data);
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
        $info = VenueCourseModel::where('id', 'in', $ids)->update($data);

        if ($info) {
            return $this->success('批量删除成功', url('index'));
        } else {
            return $this->error('批量删除失败');
        }
    }
}