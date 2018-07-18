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
//        dump($list2);exit;

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
//        dump($data);exit;

        return $this->fetch();
    }
}