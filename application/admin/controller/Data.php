<?php
namespace app\admin\controller;
use think\Db;

/**
 * Created by PhpStorm.
 * User: laowang
 * Date: 2017/7/26
 * Time: 15:59
 */

class Data extends Admin
{   
    // 两学一做   数据统计
    public function index(){
        // 获取 每日一课  答题人数
        // 周
        $week = Db::name('answers')->whereTime('create_time','w')->count();
        // 月
        $mon = Db::name('answers')->whereTime('create_time','m')->count();
        // 年
        $year = Db::name('answers')->whereTime('create_time','y')->count();
        $this->assign('msg',[$week,$mon,$year]);
        // 获取  在线答题  答题人数
        // 周
        $w = Db::name('answer_data')->whereTime('create_time','w')->count();
        // 月
        $m = Db::name('answer_data')->whereTime('create_time','m')->count();
        // 年
        $y = Db::name('answer_data')->whereTime('create_time','y')->count();
        $this->assign('line',[$w,$m,$y]);
        return $this->fetch();
    }
}