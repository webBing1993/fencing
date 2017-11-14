<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/10
 * Time: 9:03
 */

namespace app\home\controller;


class Activity extends Base
{
    /**
     * @return mixed  主页
     */
    public function index(){

        return $this ->fetch();
    }

    /*
    *  更多活动
    */
    public  function morelist(){

        return $this ->fetch();
    }

    /*
     *  发布和填写
     */
    public  function publish(){
        return $this ->fetch();
    }



}