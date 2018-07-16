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
    public function news(){
        return $this->fetch();
    }
    public function daquan(){
        return $this->fetch();
    }
    public function style(){
        return $this->fetch();
    }
    public function fencing(){
        return $this->fetch();
    }
    public function jjdetail(){
        return $this->fetch();
    }
    public function newsmore(){
        return $this->fetch();
    }
    public function newsdetail(){
        return $this->fetch();
    }
}