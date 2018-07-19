<?php
/**
 * Created by PhpStorm.
 * User: 王泽锋
 * Date: 2018/07/18
 * Time: 09:47
 */

namespace app\home\controller;

use app\home\model\News;
use app\home\model\Venue;
use app\home\model\Notice;
use app\home\model\Knowledge;
use app\home\model\Show;

class Association  extends Base
{
    //击剑风云首页
    public function index(){
        //场馆显示4个
        $list1 = Venue::where('status',0)->order('id desc')->limit(4)->select();

        foreach($list1 as $key=>$v){
            $li = json_decode($v['front_cover']);
            $list1[$key]['images'] = $li[0];
        }
        $this->assign('list1',$list1);

        //新闻动态2条
        $list2 = News::where('status',0)->order('id desc')->limit(2)->select();
        $this->assign('list2',$list2);

        return $this->fetch();
    }

    //通知公告 列表页
    public function news(){
        //训练通知
        $map1 = array('type' => 1, 'status' => 0);
        $left = Notice::where($map1)->order('id desc')->limit(10)->select();
        $this->assign('left',$left);
        //赛事通知
        $map2 = array('type' => 2, 'status' => 0);
        $center = Notice::where($map2)->order('id desc')->limit(10)->select();
        $this->assign('center',$center);
        //比赛成绩
        $map3 = array('type' => 3, 'status' => 0);
        $right = Notice::where($map3)->order('id desc')->limit(10)->select();
        $this->assign('right',$right);

        return $this->fetch();
    }

    //通知公告 详情页
    public function newsdetail2(){
        $Id = input('id');
        $data = Notice::where('id',$Id)->find();
        $this->assign('data',$data);

        return $this->fetch('newsdetail');
    }

    //击剑百科
    public function daquan(){
        //重剑
        $map1 = array('type' => 1, 'status' => 0);
        $left = Knowledge::where($map1)->order('id desc')->limit(10)->select();
        $this->assign('left',$left);
        //花剑
        $map2 = array('type' => 2, 'status' => 0);
        $center = Knowledge::where($map2)->order('id desc')->limit(10)->select();
        $this->assign('center',$center);
        //佩剑
        $map3 = array('type' => 3, 'status' => 0);
        $right = Knowledge::where($map3)->order('id desc')->limit(10)->select();
        $this->assign('right',$right);

        return $this->fetch();
    }

    //击剑百科 详情页
    public function newsdetail3(){
        $Id = input('id');
        $data = Knowledge::where('id',$Id)->find();
        $this->assign('data',$data);

        return $this->fetch('newsdetail');
    }

    //风采展示
    public function style(){
        //教练员
        $map1 = array('type' => 1, 'status' => 0);
        $left = Show::where($map1)->order('id desc')->limit(10)->select();
        $this->assign('left',$left);
        //学员
        $map2 = array('type' => 2, 'status' => 0);
        $right = Show::where($map2)->order('id desc')->limit(10)->select();
        $this->assign('right',$right);

        return $this->fetch();
    }

    //首页场馆模块 更多页
    public function fencing(){
        $list = Venue::where('status',0)->order('id desc')->select();

        foreach($list as $key=>$v){
            $li = json_decode($v['front_cover']);
            $list[$key]['images'] = $li[0];
        }
        $this->assign('list',$list);

        return $this->fetch();
    }

    //场馆模块详情页
    public function jjdetail(){
        $Id = input('id');
        $data = Venue::where('id',$Id)->find();
        $data['front_cover'] = json_decode($data['front_cover']);
        $this->assign('data',$data);

        return $this->fetch();
    }

    //新闻动态模块  更多页
    public function newsmore(){
        $list = News::where('status',0)->order('id desc')->limit(10)->select();
        $this->assign('list',$list);

        return $this->fetch();
    }

    //新闻动态 详情页
    public function newsdetail(){
        $Id = input('id');
        $data = News::where('id',$Id)->find();
        $this->assign('data',$data);

        return $this->fetch();
    }
}