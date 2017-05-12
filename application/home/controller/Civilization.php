<?php
/**
 * Created by PhpStorm.
 * User: stiff <1570004138@163.com>
 * Date: 2017/5/12
 * Time: 10:50
 */
namespace app\home\controller;

class Civilization extends Base{
    /**
     * 文明创建
     */
    public function index(){
        $this ->assign('relevant',0);
        $this ->assign('party',0);
        return $this ->fetch();
    }
    /**
     * 志愿发布更多列表
     */
    public function relevantlist(){
        return $this ->fetch();
    }
}