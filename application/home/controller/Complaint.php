<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/20
 * Time: 13:57
 */

namespace app\home\controller;


class Complaint extends Base
{
    public function add(){
        return $this->fetch();
    }
}