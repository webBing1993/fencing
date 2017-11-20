<?php
/**
 * Created by PhpStorm.
 * User: Lxx<779219930@qq.com>
 * Date: 2016/10/8
 * Time: 10:29
 */

namespace app\admin\controller;

use app\admin\model\Notice as NoticeModel;
use app\admin\model\Picture;
use app\admin\model\Push;
use com\wechat\TPQYWechat;
use think\Config;

/**
 * Class Notice
 * @package  支部活动
 */
class Notice extends Admin {
    /**
     * 活动安排
     */
    public function index(){
        $map = array(
            'type' => 1,
            'status' => array('egt',0),
        );
        $list = $this->lists('Notice',$map);
        int_to_string($list,array(
            'status' => array(0=>"待审核",1=>"已发布",2=>"审核未通过"),
            'recommend' => array(0 => "否" ,1 => "是")
        ));
        $list2['type']=$list['0']['type'];
        //dump($list2);
        //exit();
        $this->assign('list2',$list2);
        
        $this->assign('list',$list);
        return $this->fetch();
    }
    /**
     * 活动安排 添加
     */
    public function indexadd($type){
        $this->assign('type',$type);
        if(IS_POST) {
            $data = input('post.');
            //dump($data);
            //exit();
            $result = $this->validate($data,'Notice');  // 验证  数据
            $data['create_user'] = $_SESSION['think']['user_auth']['id'];
            if (true !== $result) {
                return $this->error($result);
            }else{
                $noticeModel = new NoticeModel();
                $data['start_time'] = strtotime($data['start_time']);
                $data['end_time'] = strtotime($data['end_time']);
                if (!empty($data['start_time']) && empty($data['end_time'])){
                    return $this->error('请添加结束时间');
                }
                if (empty($data['start_time']) && !empty($data['end_time'])){
                    return $this->error('请添加开始时间');
                }
                if (!empty($data['start_time']) && !empty($data['end_time']) && $data['end_time'] <= $data['start_time']){
                    return $this->error('结束时间有错误');
                }
                $res = $noticeModel->save($data);
                if ($res){
                    if ($data['type']==1) {
                        return $this->success("添加活动安排成功", Url('Notice/index'));
                    }else{
                        return $this->success("添加会议纪要成功", Url('Notice/meeting'));
                    }
                }else{
                    return $this->error($noticeModel->getError());
                }
            }
        }else {
            return $this->fetch();
        }
    }
    /**
     * 活动安排 修改
     */
    public function indexedit(){
        if(IS_POST) {     
            $data = input('post.');
            $result = $this->validate($data,'Notice');  // 验证  数据
            $data['create_user'] = $_SESSION['think']['user_auth']['id'];
            if (true !== $result) {
                return $this->error($result);
            }else{
                $noticeModel = new NoticeModel();
                if (!empty($data['start_time']) && empty($data['end_time'])){
                    return $this->error('请添加结束时间');
                }
                if (empty($data['start_time']) && !empty($data['end_time'])){
                    return $this->error('请添加开始时间');
                }
                if (!empty($data['start_time']) && !empty($data['end_time']) && $data['end_time'] <= $data['start_time']){
                    return $this->error('结束时间有错误');
                }
                $data['start_time'] = strtotime($data['start_time']);
                $data['end_time'] = strtotime($data['end_time']);
                $res = $noticeModel->save($data,['id'=>$data['id']]);
                if ($res){
                    if ($data['type']==1) {
                        return $this->success("修改活动安排成功", Url('Notice/index'));
                    }else{
                        return $this->success("修改会议纪要成功", Url('Notice/meeting'));
                    }
                }else{
                    return $this->get_update_error_msg($noticeModel->getError());
                }
            }
        }else{
            $id = input('id');
            $msg = NoticeModel::get($id);
            $this->assign('msg',$msg);
            return $this->fetch();
        }
    }
    /**
     * 删除
     */
    public function del(){
        $id = input('id');
        if (empty($id)){
            return $this->error('系统参数错误');
        }
        $map['status'] = "-1";
        $info = NoticeModel::where('id',$id)->update($map);
        if($info) {
            return $this->success("删除成功");
        }else{
            return $this->error("删除失败");
        }
    }

    //会议纪要
    public function meeting(){

            $map = array(
                'type' => 3,
                'status' => array('egt',0),
            );
            $list = $this->lists('Notice',$map);
            int_to_string($list,array(
                'status' => array(0=>"待审核",1=>"已发布",2=>"审核未通过"),
                'recommend' => array(0 => "否" ,1 => "是")
            ));
        $list2['type']=$list['0']['type'];
        //dump($list2);
        //exit();
        $this->assign('list2',$list2);
            $this->assign('list',$list);
            return $this->fetch('Notice/index');

    }
   
    /**
     * 活动展示
     */
    public function show(){
        $map = array(
            'type' => 2,
            'status' => array('egt',0),
        );
        $list = $this->lists('Notice',$map);
        int_to_string($list,array(
            'status' => array(0=>"待审核",1=>"已发布",2=>"审核未通过"),
            'recommend' => array(0 => "否" ,1 => "是")
        ));

        $list2['type']=$list['0']['type'];
        //dump($list2);
        //exit();
        $this->assign('list2',$list2);
        $this->assign('list',$list);
        return $this->fetch();
    }

    /**
     * 活动展示 添加
     */
    public function showadd($type){
       $this->assign('type',$type);
        if(IS_POST) {
            $data = input('post.');
    //dump($data);
            //exit();

            $result = $this->validate($data,'Activity');  // 验证  数据
            $data['create_user'] = $_SESSION['think']['user_auth']['id'];
            if (true !== $result) {
                return $this->error($result);
            }else{
                $noticeModel = new NoticeModel();

                $res = $noticeModel->save($data);
                if ($res){
                    if ($data['type']==2) {
                        return $this->success("添加活动成功", Url('Notice/show'));
                    }else{
                        return $this->success("添加活动成功", Url('Notice/activity'));
                    }
                }else{
                    return $this->error($noticeModel->getError());
                }
            }
        }else {

            return $this->fetch();
        }
    }

    /**
     * 活动展示 修改
     */
    public function showedit(){
        //$data = input('post.');
        //dump($data);
        //exit();
        if(IS_POST) {
            $data = input('post.');

            $result = $this->validate($data,'Activity');  // 验证  数据
            $data['create_user'] = $_SESSION['think']['user_auth']['id'];
            if (true !== $result) {
                return $this->error($result);
            }else{
                $noticeModel = new NoticeModel();

                $res = $noticeModel->save($data,['id'=>$data['id']]);
                if ($res){
                    if ($data['type']==2) {
                        return $this->success("修改活动展示成功", Url('Notice/show'));
                    }else{
                        return $this->success("修改固定活动成功", Url('Notice/activity'));
                    }

                }else{
                    return $this->get_update_error_msg($noticeModel->getError());
                }
            }
        }else{
            $id = input('id');
            $msg = NoticeModel::get($id);
            $this->assign('msg',$msg);
            return $this->fetch();
        }
    }

    /**
     * 固定活动
     */
    public function activity(){
        $map = array(
            'type' => 4,
            'status' => array('egt',0),
        );
        $list = $this->lists('Notice',$map);
        int_to_string($list,array(
            'status' => array(0=>"待审核",1=>"已发布",2=>"审核未通过"),
            'recommend' => array(0 => "否" ,1 => "是")
        ));
        //dump($list);
        //exit();
       $list2['type']=$list['0']['type'];
        //dump($list2);
        //exit();
        $this->assign('list2',$list2);
        $this->assign('list',$list);
        return $this->fetch('Notice/show');
    }
    
}