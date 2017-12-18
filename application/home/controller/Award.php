<?php
/**
 * Created by PhpStorm.
 * User: laowang
 * Date: 2017/12/18
 * Time: 上午9:55
 */

namespace app\home\controller;

use think\Controller;
class Award extends Controller
{
    /**
     * 首页
     */
    public function index(){
        return $this->fetch();
    }
    /**
     * 答题页面
     */
    public function answer(){
        return $this->fetch();
    }
}