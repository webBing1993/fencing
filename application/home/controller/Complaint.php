<?php
/**
 * Created by PhpStorm.
 * User: 王泽锋
 * Date: 2018/07/20
 * Time: 14:38
 */

namespace app\home\controller;


class Complaint extends Base
{
    //教练投诉首页
    public function index(){

      return $this->fetch();
    }

    //教练投诉  添加
    public function add(){
        $data = input('post.');
//        dump($data);exit;

        return $this->fetch();
    }


}