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
    public function index(){
        $userId = session('userId');
        $user = WechatUser::where('mobile',$userId)->find();
        $this->assign('user',$user);

        return $this->fetch();
    }
    public function information(){
        return $this->fetch();
    }
    public function sign(){
        return $this->fetch();
    }
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
        $left = Apply::where('create_user',$userId)->where('status',0)->order('id desc')->limit(6)->select();//待审核
        $right = Apply::where('create_user',$userId)->where('status','>',0)->order('id desc')->limit(6)->select();//已审核
        $this->assign('left',$left);
        $this->assign('right',$right);

        return $this->fetch();
    }

    public function leavedetail(){
        $Id = input('id');
        $data = Apply::where('id',$Id)->find();
        $user = WechatUser::where('mobile',$data['create_user'])->find();
        $data['create_user'] = $user['name'];
        if(!empty($data['front_cover'])){
            $data['front_cover'] = json_decode($data['front_cover']);
        }
        $this->assign('data',$data);
//        dump($data);exit();

        return $this->fetch();
    }

    //个人中心  请假申请(申请页面)
    public function application(){

        return $this->fetch();
    }

    //个人中心  请假申请 提交数据库
    public function apply(){
        $data = input('post.');
        if(!empty($data['front_cover'])){
            $data['front_cover'] = json_encode($data['front_cover']);
        }
        $data['create_user'] = session('userId');
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