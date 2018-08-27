<?php
/**
 * Created by PhpStorm.
 * User: 王泽锋
 * Date: 2018/07/17
 * Time: 09:28
 */
namespace app\admin\controller;

use app\home\model\CompetitionApply;
use think\Controller;
use app\admin\model\Picture;
use app\admin\model\Push;
use com\wechat\TPQYWechat;
use app\admin\model\Competition as CompetitionModel;
use app\admin\model\Venue;
use app\admin\model\Course;
use think\Config;

/**
 * Class CourseSign
 * @package  课程签到控制器
 */
class CourseSign extends Admin {
    /**
     * 主页列表
     */
    public function index(){
        $num = 7;
        $arr = ['一','二','三','四','五','六','日'];
        $next_monday = date('Y-m-d', strtotime('+1 sunday +1 days', time()));

        $list = [];
        for ($i = 0; $i < $num; $i++) {
            $list[$i]['id'] = date('Y-m-d', strtotime('+'.$i.' days', strtotime($next_monday)));
            $list[$i]['name'] = '下周'.$arr[$i].' · '.date('Y-m-d', strtotime('+'.$i.' days', strtotime($next_monday)));
        }
        $this->assign('list',$list);

        return $this->fetch();
    }
    //场馆
    public function venue(){
        $date = input('date');
        $type = input('type');
        $list = Venue::where(['status' => ['>=', 0]])->select();
        $this->assign('date',$date);
        $this->assign('type',$type);
        $this->assign('list',$list);

        return $this->fetch();
    }
    //课程
    public function course(){
        $date = input('date');
        $type = input('type');
        $venue = input('venue');
        $list = Course::where(['venue_id' => $venue, 'status' => ['>=', 0]])->select();
        $this->assign('date',$date);
        $this->assign('type',$type);
        $this->assign('venue',$venue);
        $this->assign('list',$list);

        return $this->fetch();
    }

}