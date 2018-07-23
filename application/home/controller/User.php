<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/20
 * Time: 13:58
 */

namespace app\home\controller;


class User extends Base
{
    public function index(){
        return $this->fetch();
    }
}