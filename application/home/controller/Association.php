<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/16
 * Time: 10:47
 */

namespace app\home\controller;


class Association  extends Base
{
    public function index(){
        return $this->fetch();
    }
}