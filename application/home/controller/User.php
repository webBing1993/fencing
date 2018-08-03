<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/20
 * Time: 13:58
 */

namespace app\home\controller;


use app\home\model\Apply;
use app\home\model\WechatUser;

class User extends Base
{
    //个人中心首页
    public function index(){
        $userId = session('userId');
        $user = WechatUser::where('mobile',$userId)->find();
        $this->assign('user',$user);

        return $this->fetch();
    }

    //个人信息页
    public function information(){
        $userId = session('userId');
        $user = WechatUser::where('mobile',$userId)->find();
        if($user['gender'] == 0){
            $user['gender'] = '未定义';
        }elseif($user['gender'] == 1){
            $user['gender'] = '男';
        }else{
            $user['gender'] = '女';
        }
        $this->assign('user',$user);

        return $this->fetch();
    }

    public function sign(){
        return $this->fetch();
    }

    //会员申请页
    public function insider(){
        $userId = session('userId');
        $user = WechatUser::where('mobile',$userId)->find();
        $this->assign('user',$user);

        return $this->fetch();
    }
    public function rule(){
        return $this->fetch();
    }
    public function paysuccess(){
        return $this->fetch();
    }
    public function train(){
        return $this->fetch();
    }
    public function play(){
        return $this->fetch();
    }

    //个人中心  请假申请(列表首页)
    public function leave(){
        $userId = session('userId');
        $user = WechatUser::where('mobile',$userId)->find();
        $this->assign('user',$user);
        if($user['tag'] == 1){
            $data = Apply::where('create_user',$userId)->order('id desc')/*->limit(6)*/->select();
            $this->assign('data',$data);
        }else{
            //待审核
            $map = array('leave' => array('eq',$userId),'leavezt' => array('eq',0));
            $q = Apply::where($map);
            $left = $q->whereOr(function($q)use($userId){
                $map2 = array('leavetwo' => array('eq',$userId),'leavetwozt' => array('eq',0),'leavezt' => array('eq',1));
                $q->where($map2);
            })->order('id desc')/*->limit(6)*/->select();//待审核
//            dump($left->fetchSql()->select());exit;////sql语句查询
            //已审核
            $m = array('leave' => array('eq',$userId),'leavezt' => array('neq',0));
            $b = Apply::where($m);
            $right = $b->whereOr(function($b)use($userId){
                $m2 = array('leavetwo' => array('eq',$userId),'leavetwozt' => array('neq',0));
                $b->where($m2);
            })->order('id desc')/*->limit(6)*/->select();//已审核
            $this->assign('left',$left);
            $this->assign('right',$right);
        }

        return $this->fetch();
    }

    public function leavedetail(){
        //判断身份
        $userId = session('userId');
        $user = WechatUser::where('mobile',$userId)->find();
        $this->assign('user',$user);
        //获取该请假信息
        $Id = input('id');
        $data = Apply::where('id',$Id)->find();
        //请假人姓名
        $u = WechatUser::where('mobile',$data['create_user'])->find();
        $data['create_user'] = $u['name'];
        //一级审核人姓名
        if(!empty($data['leave'])){
            $a = WechatUser::where('mobile',$data['leave'])->find();
            $data['leave'] = $a['name'];
            $data['img'] = ($a['header']) ? $a['header'] : $a['avatar'];
        }
        //二级审核人姓名
        if(!empty($data['leavetwo'])){
            $b = WechatUser::where('mobile',$data['leavetwo'])->find();
            $data['leavetwo'] = $b['name'];
            $data['img2'] = ($b['header']) ? $b['header'] : $b['avatar'];
        }
        //图片
        if(!empty($data['front_cover'])){
            $data['front_cover'] = json_decode($data['front_cover']);
        }
        $this->assign('data',$data);
        //判断时候已经审批,审批框是否显示
        $li = Apply::where('id',$Id)->find();
        if($userId == $li['leave']){
            if($data['leavezt'] == 0){
                $sp = 1;//显示
            }else{
                $sp = 0;//不显示
            }
        }else{
            if($data['leavetwozt'] == 0){
                $sp = 1;//显示
            }else{
                $sp = 0;//不显示
            }
        }
        $this->assign('sp',$sp);

        return $this->fetch();
    }

    //审批提交 意见
    public function view(){
        $data = input('post.');
        $userId = session('userId');
        $list = Apply::where('id',$data['id'])->find();
        if($list['leave'] == $userId){
            $da['id'] = $data['id'];
            $da['leavetext'] = $data['leavetext'];
            $da['leavetime'] = time();
            $da['leavezt'] = $data['status'];
            if($data['status'] == 2){
                $da['status'] = $data['status'];
            }elseif($data['status'] == 1){
                if(empty($list['leavetwo'])){
                    $da['status'] = $data['status'];
                }
            }
            $info = Apply::update($da);
        }elseif($list['leavetwo'] == $userId){
            $da['id'] = $data['id'];
            $da['leavetwotext'] = $data['leavetext'];
            $da['leavetwotime'] = time();
            $da['leavetwozt'] = $data['status'];
            $da['status'] = $data['status'];
            $info = Apply::update($da);
        }

        if($info) {
            return $this->success("审批成功");
        }else{
            return $this->error("审批失败");
        }
    }

    //个人中心  请假申请(申请页面)
    public function application(){
        $userId = session('userId');
        $user = WechatUser::where('mobile',$userId)->find();
        if(!empty($user['telephone'])){
            $name1 = WechatUser::where('mobile',$user['telephone'])->find();
            $data['name1'] = $name1['name'];
            $data['img1'] = ($name1['header']) ? $name1['header'] : $name1['avatar'];
        }else{
            $data['name1'] = '';
            $data['img1'] = '';
        }
        if(!empty($user['telephone2'])){
            $name2 = WechatUser::where('mobile',$user['telephone2'])->find();
            $data['name2'] = $name2['name'];
            $data['img2'] = ($name2['header']) ? $name2['header'] : $name2['avatar'];
        }else{
            $data['name2'] = '';
            $data['img2'] = '';
        }
        $this->assign('data',$data);

        return $this->fetch();
    }

    //个人中心  请假申请 提交数据库
    public function apply(){
        $userId = session('userId');
        $data = input('post.');
        $user = WechatUser::where('mobile',$userId)->find();

        if(!empty($user['telephone'])){
            $data['leave'] = $user['telephone'];
        }//一级审批者
        if(!empty($user['telephone2'])){
            $data['leavetwo'] = $user['telephone2'];
        }//二级审批者
        if(!empty($data['front_cover'])){
            $data['front_cover'] = json_encode($data['front_cover']);
        }//图片json
        $data['create_user'] = $userId;
        $data['create_time'] = time();
        $info = Apply::create($data);

        if($info) {
            return $this->success("提交成功");
        }else{
            return $this->error("提交失败");
        }
    }

    public function reite(){
        return $this->fetch();
    }
    public function reite01(){
        return $this->fetch();
    }
}