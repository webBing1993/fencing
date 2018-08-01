<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/20
 * Time: 13:58
 */

namespace app\home\controller;


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
    public function leave(){
        return $this->fetch();
    }
    public function leavedetail(){
        return $this->fetch();
    }

    //个人中心  请假申请(申请页面)
    public function application(){

        return $this->fetch();
    }

    //个人中心  请假申请 提交数据库
    public function apply(){
        $data = input('post');
        dump($data);exit;


        return $this->fetch();
    }


    public function reite(){
        return $this->fetch();
    }
    public function reite01(){
        return $this->fetch();
    }
}