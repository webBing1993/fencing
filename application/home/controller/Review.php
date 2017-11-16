<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/16 0016
 * Time: 上午 9:47
 */

namespace app\home\controller;


class Review extends Base{

    public function index(){

        return $this->fetch();

    }
}