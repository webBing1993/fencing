<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/6
 * Time: 14:36
 */

namespace app\home\controller;


class Sign extends Base
{
    public function index(){
        return $this->fetch();
    }
    public function signS(){
        return $this->fetch();
    }
}