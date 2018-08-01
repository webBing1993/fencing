<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/19
 * Time: 14:16
 */

namespace app\home\controller;


/*
 * 通讯名录
 * */
class Newsletter  extends Base
{
    // 通讯名录首页
    public function index(){


        return $this->fetch();
    }


}