<?php
/**
 * Created by PhpStorm.
 * User: stiff <1570004138@163.com>
 * Date: 2017/4/21
 * Time: 15:12
 */
namespace app\home\controller;

class Tourist extends Base{
    /**
     * 游客模式二维码
     */
    public function erweima(){
        return $this ->fetch();
    }
    /**
     * 游客模式登录页
     */
    public function tourist(){
        return $this ->fetch();
    }
    /**
     * 游客模式仿企业号首页
     */
    public function index(){
        return $this ->fetch();
    }
    /**
     * 小镇动态
     */
    public function touristdetails1(){
        return $this ->fetch();
    }
    /**
     * 两学一做
     */
    public function touristdetails2(){
        return $this ->fetch();
    }
}