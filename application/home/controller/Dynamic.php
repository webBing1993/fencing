<?php
/**
 * Created by PhpStorm.
 * User: stiff <1570004138@163.com>
 * Date: 2017/5/10
 * Time: 16:08
 */
namespace app\home\controller;

class Dynamic extends Base{
    /**
     * 党委动态
     */
    public function index(){
        return $this ->fetch();
    }
    /**
     * 党委动态详情页
     */
    public function detail(){
        return $this ->fetch();
    }
}