<?php
/**
 * Created by PhpStorm.
 * User: 王泽锋
 * Date: 2018/07/18
 * Time: 09:47
 */

namespace app\home\controller;

use app\admin\model\CompetitionApply;
use app\home\model\CompetitionEvent;
use app\home\model\Competition;
use app\home\model\CompetitionGroup;
use app\home\model\News;
use app\home\model\Picture;
use app\home\model\Venue;
use app\home\model\Notice;
use app\home\model\Knowledge;
use app\home\model\Show;
use app\home\model\WechatUser;

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

    //通知公告   上拉加载
    public function more(){
        $type = input('type');
        $len = input('len');
        $map = array('type' => $type, 'status' => 0);
        $info = Notice::where($map)->order('id desc')->limit($len,6)->select();

        foreach($info as $value){
            $value['time'] = date("Y-m-d",$value['create_time']);
            $img = Picture::get($value['front_cover']);
            $value['front_cover'] = $img['path'];
        }
        if($info){
            return $this->success("加载成功",'',$info);
        }else{
            return $this->error("加载失败");
        }
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

    //击剑百科   上拉加载
    public function more2(){
        $type = input('type');
        $len = input('len');
        $map = array('type' => $type, 'status' => 0);
        $info = Knowledge::where($map)->order('id desc')->limit($len,6)->select();

        foreach($info as $value){
            $value['time'] = date("Y-m-d",$value['create_time']);
            $img = Picture::get($value['front_cover']);
            $value['front_cover'] = $img['path'];
        }
        if($info){
            return $this->success("加载成功",'',$info);
        }else{
            return $this->error("加载失败");
        }
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

    //风采展示 点击接口
    public function show(){
        $id = input('id');
        $data = Show::where('id',$id)->find();
        if($data){
            return $this->success("获取数据成功",'',$data);
        }else{
            return $this->error("获取数据失败");
        }
    }


    //风采展示   上拉加载
    public function more3(){
        $type = input('type');
        $len = input('len');
        $map = array('type' => $type, 'status' => 0);
        $info = Show::where($map)->order('id desc')->limit($len,6)->select();

        foreach($info as $value){
            $value['time'] = date("Y-m-d",$value['create_time']);
            $img = Picture::get($value['front_cover']);
            $value['front_cover'] = $img['path'];
        }
        if($info){
            return $this->success("加载成功",'',$info);
        }else{
            return $this->error("加载失败");
        }
    }

    //首页场馆模块 更多页
    public function fencing(){
        $list = Venue::where('status',0)->order('id desc')->limit(10)->select();

        foreach($list as $key=>$v){
            $li = json_decode($v['front_cover']);
            $list[$key]['images'] = $li[0];
        }
        $this->assign('list',$list);

        return $this->fetch();
    }

    //场馆   上拉加载
    public function more4(){
        $len = input('len');
        $map = array('status' => 0);
        $info = Venue::where($map)->order('id desc')->limit($len,6)->select();

        foreach($info as $key=>$v){
            $li = json_decode($v['front_cover']);
            $pic = Picture::where('id',$li[0])->value('path');
            $info[$key]['front_cover'] = $pic;
            $value['time'] = date("Y-m-d",$v['create_time']);
        }

        if($info){
            return $this->success("加载成功",'',$info);
        }else{
            return $this->error("加载失败");
        }
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

    //新闻动态   上拉加载
    public function more5(){
        $len = input('len');
        $map = array('status' => 0);
        $info = News::where($map)->order('id desc')->limit($len,6)->select();

        foreach($info as $value){
            $value['time'] = date("Y-m-d",$value['create_time']);
            $img = Picture::get($value['front_cover']);
            $value['front_cover'] = $img['path'];
        }
        if($info){
            return $this->success("加载成功",'',$info);
        }else{
            return $this->error("加载失败");
        }
    }

    //新闻动态 详情页
    public function newsdetail(){
        $Id = input('id');
        $data = News::where('id',$Id)->find();
        $this->assign('data',$data);

        return $this->fetch();
    }

    /**
     * 比赛报名列表
     */
    public function game(){
        //比赛报名
        $map1 = array('status' => ['>=', 0]);
        $left = Competition::where($map1)->order('end_time desc')->limit(10)->select();
        $this->assign('left',$left);
        //课程报名
        $map2 = array('type' => 2, 'status' => 0);
        $right = Show::where($map2)->order('id desc')->limit(10)->select();
        $this->assign('right',$right);

        return $this->fetch();
    }
    //比赛报名   上拉加载
    public function more6(){
        $len = input('len');
        $map = array('status' => ['>=', 0]);
        $info = Competition::where($map)->order('end_time desc')->limit($len,6)->select();

        foreach($info as $value){
            $value['time'] = date("Y-m-d",$value['end_time']);
            $img = Picture::get($value['front_cover']);
            $value['front_cover'] = $img['path'];
        }
        if($info){
            return $this->success("加载成功",'',$info);
        }else{
            return $this->error("加载失败");
        }
    }

    /**
     * 我要报名比赛详情页
     */
    public function gamedetail(){
        $id = input('id');
        $data = Competition::get($id);
        $data['individual_event'] = competitionEvent::where(['status' => 0, 'competition_id' => $id, 'type' => competitionEvent::INDIVIDUAL_EVENT])->order('sort')->select();
        $data['team_event'] = competitionEvent::where(['status' => 0, 'competition_id' => $id, 'type' => competitionEvent::TEAM_EVENT])->order('sort')->select();
        $data['competition_group'] = competitionGroup::where(['status' => 0, 'competition_id' => $id])->order('sort')->select();
        $this->assign('data',$data);

        return $this->fetch();
    }

    /**
     * 马上报名提交页
     */
    public function gamedetail02(){
        $id = input('id');
        $userId = session('userId');
        $data = Competition::get($id);
        $model = wechatUser::where(['userid' => $userId])->find();
//        if(!$model['name'] || !$model['birthday'] || !$model['gender'] || !$model['guardian_mobile'] || !$model['address']){
//            $data['show_tip'] = true;
//        }else{
//            $data['show_tip'] = false;
//        }
        //TODO
        $representative = "杭州击剑馆";
        $coach = "林教练1";
        $individual_event = competitionEvent::where(['status' => 0, 'competition_id' => $id, 'type' => competitionEvent::INDIVIDUAL_EVENT])->find();
        $team_event = competitionEvent::where(['status' => 0, 'competition_id' => $id, 'type' => competitionEvent::TEAM_EVENT])->find();
        if ($individual_event && $team_event) {
            $show_type = 3;
        } else {
            if ($team_event) {
                $show_type = 2;
            } else {
                $show_type = 1;
            }
        }

        $this->assign('data',$data);
        $this->assign('model',$model);
        $this->assign('representative',$representative);
        $this->assign('coach',$coach);
        $this->assign('show_type',$show_type);

        return $this->fetch();
    }

    /**
     * 报名时组别列表
     */
    public function getgrouplist(){
        $id = input('id');
        $userId = session('userId');
        if (!$id) {
            return $this->error('参数缺失');
        }
        $birthday = wechatUser::where(['userid' => $userId])->value('birthday');
        $birthday = strtotime($birthday);
        $data = competitionGroup::where(['status' => 0, 'competition_id' => $id])->order('sort')->select();
        if ($data) {
            foreach ($data as $key => $val) {
                if ($val['start_time'] > $birthday && $val['end_time'] > $birthday) {
                    unset($data[$key]);
                }
            }
        } else {
            $data = [];
        }

        return $this->success('成功','',$data);
    }

    /**
     * 报名时获取价格
     */
    public function getprice(){
        $id = input('id');
        $type = input('type');//赛别 1 个人 2 团体 3 全部
        $kinds = input('kinds');//剑种
        $userId = session('userId');

        if (!$id || !$type) {
            return $this->error('参数缺失');
        }

        $return = [];
        if ($type == 3) {//赛别 3 全部
            if ($kinds) {//单独剑种
                $model = competitionEvent::where(['status' => 0, 'competition_id' => $id, 'kinds' => $kinds])->select();
                $return['id'] = $kinds;
                $return['name'] = competitionEvent::EVENT_KINDS_ARRAY[$kinds];
                $return['price'] = 0;
                $return['vip_price'] = 0;

                //价格相加
                foreach ($model as $key => $val) {
                    //会员价
                    $return['vip_price'] += $val['vip_price'];
                    //普通价
                    $return['price'] += $val['price'];
                }
            } else {//所有剑种
                $model = competitionEvent::where(['status' => 0, 'competition_id' => $id])->select();

                foreach ($model as $key => $val) {
                    $return[$val['kinds']]['id'] = $val['kinds'];
                    $return[$val['kinds']]['name'] = competitionEvent::EVENT_KINDS_ARRAY[$val['kinds']];

                    if (!isset($return[$val['kinds']]['price'])) {
                        $return[$val['kinds']]['price'] = 0;
                    }

                    if (!isset($return[$val['kinds']]['vip_price'])) {
                        $return[$val['kinds']]['vip_price'] = 0;
                    }
                    //按剑种价格相加
                    //会员价
                    $return[$val['kinds']]['vip_price'] += $val['vip_price'];
                    //普通价
                    $return[$val['kinds']]['price'] += $val['price'];
                }
                $return = array_values($return);
            }
        } else {//赛别 1 个人 2 团体
            if ($kinds) {//单独剑种
                $return['id'] = $kinds;
                $return['name'] = competitionEvent::EVENT_KINDS_ARRAY[$kinds];
                $return['price'] = 0;
                $return['vip_price'] = 0;
                $model = competitionEvent::where(['status' => 0, 'competition_id' => $id, 'type' => $type, 'kinds' => $kinds])->find();
                //会员价
                $return['vip_price'] = $model['vip_price'];
                //普通价
                $return['price'] = $model['price'];
            } else {//所有剑种
                $model = competitionEvent::where(['status' => 0, 'competition_id' => $id, 'type' => $type])->select();

                foreach ($model as $key => $val) {
                    $return[$val['kinds']]['id'] = $val['kinds'];
                    $return[$val['kinds']]['name'] = competitionEvent::EVENT_KINDS_ARRAY[$val['kinds']];

                    if (!isset($return[$val['kinds']]['price'])) {
                        $return[$val['kinds']]['price'] = 0;
                    }

                    if (!isset($return[$val['kinds']]['vip_price'])) {
                        $return[$val['kinds']]['vip_price'] = 0;
                    }
                    //按剑种价格相加
                    //会员价
                    $return[$val['kinds']]['vip_price'] = $val['vip_price'];
                    //普通价
                    $return[$val['kinds']]['price'] = $val['price'];
                }
                $return = array_values($return);
            }
        }

        return $this->success('成功','',$return);
    }

    /**
     * 马上报名提交处理页
     * @param competition_id,group_id,type,kinds,representative,coach,card_type,card_num,remark
     */
    public function submit(){
        $data = input('post.');
        $userId = session('userId');
        $flag = 0;
        if ($data['type'] == 3) {
            $data['type'] = 1;
            $flag = 1;
        }
        $wechatUserModel = wechatUser::where(['userid' => $userId])->find();
        $competitionModel = Competition::get($data['competition_id']);
        $competitionGroupModel = CompetitionGroup::get($data['group_id']);
        $competitionEventModel = CompetitionEvent::where(['status' => 0, 'competition_id' => $data['competition_id'], 'type' => $data['type'], 'kinds' => $data['kinds']])->find();
        $data['userid'] = $userId;
        $data['name'] = $wechatUserModel['name'];
        $data['title'] = $competitionModel['title'];
        $data['end_time'] = $competitionModel['end_time'];
        $data['address'] = $competitionModel['address'];
        $data['group_name'] = $competitionGroupModel['group_name'];
        $data['event_id'] = $competitionEventModel['id'];
        if ($wechatUserModel['vip']) {
            $data['price'] = $competitionEventModel['vip_price'];
        } else {
            $data['price'] = $competitionEventModel['price'];
        }

        $competitionApplyodel = new CompetitionApply();
        $model = $competitionApplyodel->validate('CompetitionApply')->save($data);
        if($model){
            if ($flag == 1) {
                $data['type'] = 2;
                $data2 = $data;
                $competitionApplyodel2 = new CompetitionApply();
                $competitionApplyodel2->validate('CompetitionApply')->save($data2);
                return $this->success('报名成功!','',['id' => $competitionApplyodel2->id, 'type' => 3]);
            }
            return $this->success('报名成功!','',['id' => $competitionApplyodel->id, 'type' => $data['type']]);
        }else{
            return $this->error($competitionApplyodel->getError());
        }
    }

    public function paysuccess(){
        $id = input('id');
        if (!$id) {
            return $this->error('参数缺失');
        }
        $data = CompetitionApply::get($id);
        $data['end_time'] = date('Y-m-d', $data['end_time']);
        $data['type'] = competitionEvent::EVENT_TYPE_ARRAY[$data['type']];
        $data['kinds'] = competitionEvent::EVENT_KINDS_ARRAY[$data['kinds']];
        $this->assign('data',$data);

        return $this->fetch();
    }


    // 课程报名详情页
    public function gamedetail1() {

        return $this->fetch();
    }

    // 课程报名付款页
    public function payment() {

        return $this->fetch();
    }

    // 课程报名成功
    public function paysuccess1(){
        return $this->fetch();
    }
}