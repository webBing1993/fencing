<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/14 0014
 * Time: 下午 1:04
 */

namespace app\home\controller;
//签到

class Signin extends Base {
    /**
     * 主页
     */
    public function index(){

        return $this->fetch();
    }

    /**
     * 详情页
     */
    public function detail(){

        return $this->fetch();

    }
}