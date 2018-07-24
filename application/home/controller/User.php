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
    public function information(){
        return $this->fetch();
    }
    public function sign(){
        return $this->fetch();
    }
    public function insider(){
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
}