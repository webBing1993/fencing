<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/20
 * Time: 13:58
 */

namespace app\home\controller;


use app\admin\model\WechatTag;
use app\home\model\Apply;
use app\home\model\ClassRecord;
use app\home\model\Competition;
use app\home\model\CompetitionApply;
use app\home\model\Course;
use app\home\model\CourseApply;
use app\home\model\Picture;
use app\home\model\SignStatistics;
use app\home\model\Venue;
use app\home\model\VenueCourse;
use app\home\model\Vipapply;
use app\home\model\WechatUser;
use app\home\model\WechatUserTag;
use com\wechat\QYWechat;
use com\wechat\TPQYWechat;
use think\Config;

class User extends Base
{
    //个人中心首页
    public function index(){
        $userId = session('userId');
        $user = WechatUser::where('userid',$userId)->find();
        $this->assign('user',$user);
        $venue_id = WechatUserTag::getVenueId($userId);
        if($venue_id == false AND $user['train'] == 0){
            $zx = 0;//非在训
        }else{
            $zx = 1;//在训
        }
        $this->assign('zx',$zx);
        if($venue_id != false AND $user['train'] == 1 AND $user['tag'] == 1){
            $ts = 1;
        }else{
            $ts = 0;
        }
        //所属教练模块显示(在训学员模式下)
        $venue_id = WechatUserTag::getVenueId($userId);
        if($venue_id == false){
            $coach = 0;
        }else{
            $coach = 1;
        }
        $this->assign('coach',$coach);
        $this->assign('ts',$ts);
        //会员过期验证更新
        if(!empty($user['viptime'])){
            if($user['viptime'] < time()){
                $map['vip'] = 0;
                WechatUser::where('userid',$userId)->update($map);
            }
        }

        return $this->fetch();
    }

    /**
     * 设置头像
     */
    public function setHeader(){
        $userId = session('userId');
        $header = input('header');
        $map = array(
            'header' => $header,
        );
        $info = WechatUser::where('userid',$userId)->update($map);
        if($info){
            return $this->success("修改成功");
        }else{
            return $this->error("修改失败");
        }
    }

    //个人信息页
    public function information(){
        $userId = session('userId');
        $user = WechatUser::where('userid',$userId)->find();
        if($user['gender'] == 0){
            $user['gender'] = '未定义';
        }elseif($user['gender'] == 1){
            $user['gender'] = '男';
        }else{
            $user['gender'] = '女';
        }
        $this->assign('user',$user);
        return $this->fetch();
    }

    //个人中心  个人信息 保存
    public function hold(){
        $data = input('post.');
        $info = WechatUser::update($data);

        if($info){
            $model = WechatUser::where('id',$data['id'])->find();
            $param = array(
                'userid' => $model['userid'],
                'name' => $model['name'],
                //'mobile' => $data['mobile'],
                'gender' => $model['gender'],
                'extattr' => ['attrs' => array(
                    ["name" => "剑种", "value" => $model['swords']],
                    ["name" => "出生日期", "value" => $model['birthday']],
                    ["name" => "就读学校", "value" => $model['school']],
                    ["name" => "身份证号", "value" => $model['card']],
                    ["name" => "监护人", "value" => $model['guardian_mobile']],
                    ["name" => "家庭地址", "value" => $model['address']],
                    ["name" => "一级审批", "value" => $model['telephone']],
                    ["name" => "二级审批", "value" => $model['telephone2']],
                    ["name" => "抄送人1", "value" => $model['push1']],
                    ["name" => "抄送人2", "value" => $model['push2']],
                    ["name" => "抄送人3", "value" => $model['push3']],
                )]
            );
            $Wechat = new QYWechat(Config::get('mail'));
            $Wechat->updateUser($param);

            return $this->success("保存成功");
        }else{
            return $this->error("保存失败");
        }
    }

    //签到二维码 签到统计
    public function signtime(){
        $userId = session('userId');
        $user = WechatUser::where('userid',$userId)->value('openid');
        //签到记录
        $date = date('Y-m');
        $date_name = date('Y年m月');
        if (WechatUserTag::issetTag($userId, WechatTag::TAG_7)) {
            //工作人员上下班签到
            $result = SignStatistics::where(["FROM_UNIXTIME(UNIX_TIMESTAMP(`date`), '%Y-%m')" => $date, 'openid' => $user, 'type' => 2])->select();
            $res = $this->statistics($result, 2, $user);
        } else {
            if (WechatUserTag::issetTag($userId, WechatTag::TAG_3)) {
                //区域主管巡查签到
                $result = SignStatistics::where(["FROM_UNIXTIME(UNIX_TIMESTAMP(`date`), '%Y-%m')" => $date, 'openid' => $user, 'type' => 3])->select();
                $res = $this->statistics($result, 3, $user);
            } else {
                //学员教练上课签到
                $result = SignStatistics::where(["FROM_UNIXTIME(UNIX_TIMESTAMP(`date`), '%Y-%m')" => $date, 'openid' => $user, 'type' => 1])->select();
                $res = $this->statistics($result, 1, $user);
            }
        }
        $this->assign('user',$user);
        $this->assign('date_name',$date_name);
        $this->assign('time',strtotime($date));
        $this->assign('res',$res);
        $this->assign('normal',json_encode($res['normal']));
        $this->assign('late',json_encode($res['late']));
        $this->assign('absence',json_encode($res['absence']));

        return $this->fetch();
    }

    //签到统计
    public function getSign(){
        $userId = session('userId');
        $user = WechatUser::where('userid',$userId)->value('openid');
        //签到记录
        $time = input('time');//1:前一月 2：后一月
        $o_date = input('date');//2018-08
        if ($time == 1) {
            $date = date('Y-m', strtotime('-1 month', strtotime($o_date)));
        } else {
            $date = date('Y-m', strtotime('+1 month', strtotime($o_date)));
        }
        $date_name = date('Y年m月', strtotime($date));
        if (WechatUserTag::issetTag($userId, WechatTag::TAG_7)) {
            //工作人员上下班签到
            $result = SignStatistics::where(["FROM_UNIXTIME(UNIX_TIMESTAMP(`date`), '%Y-%m')" => $date, 'openid' => $user, 'type' => 2])->select();
            $res = $this->statistics($result, 2, $user);
        } else {
            if (WechatUserTag::issetTag($userId, WechatTag::TAG_3)) {
                //区域主管巡查签到
                $result = SignStatistics::where(["FROM_UNIXTIME(UNIX_TIMESTAMP(`date`), '%Y-%m')" => $date, 'openid' => $user, 'type' => 3])->select();
                $res = $this->statistics($result, 3, $user);
            } else {
                //学员教练上课签到
                $result = SignStatistics::where(["FROM_UNIXTIME(UNIX_TIMESTAMP(`date`), '%Y-%m')" => $date, 'openid' => $user, 'type' => 1])->select();
                $res = $this->statistics($result, 1, $user);
            }
        }

        return $this->success("成功",'',['name' => $date_name, 'time' => strtotime($date), 'data' => $res]);
    }

    //签到统计
    public function statistics($result, $type, $openid)
    {
        $res = array('normal' => [], 'late' => [], 'absence' => []);
        if ($result) {
            foreach ($result as $val) {
                if ($type == 1) {
                    $rs = SignStatistics::where(['openid' => $openid, 'type' => $type, 'date' => $val['date']])->select();
                    $temp = [];
                    foreach ($rs as $v) {
                        $temp[]=$v['status'];
                    }
                    $status = max($temp);
                } else {
                    $status = $val['status'];
                }
                if ($status == 1) {//正常
                    $res['normal'][] = intval(date('j', strtotime($val['date'])));
                }
                if ($status == 2 || $status == 3) {//迟到+早退
                    $res['late'][] = intval(date('j', strtotime($val['date'])));
                }
                if ($status == 4) {//缺勤
                    $res['absence'][] = intval(date('j', strtotime($val['date'])));
                }
            }

        }

        return $res;
    }

    //会员申请页
    public function insider(){
        $userId = session('userId');
        $user = WechatUser::where('userid',$userId)->find();
        if($user['gender'] == 2){
            $user['gender'] = '女';
        }elseif($user['gender'] == 1){
            $user['gender'] = '男';
        }else{
            $user['gender'] = '未定义';
        }
        if(!empty($user['viptime'])){
            $user['vipendtime'] = date("Y-m-d",$user['viptime'] + 365*24*60*60);
            $user['viptime'] = date("Y-m-d",$user['viptime']);
        }
        $this->assign('user',$user);
        $price = 120;
        $this->assign('price',$price);

        return $this->fetch();
    }

    //会员申请 去支付接口
    public function vipapply(){
        $userId = session('userId');
        $map['userid'] = $userId;
        $map['time'] = time();
        $map['endtime'] = time() + 365*24*60*60;
        $vipapplyModel = new Vipapply();
        $info = $vipapplyModel->save($map);

        if($info) {
            $nid=$vipapplyModel->id;
            return $this->success("会员申请记录成功",'',$nid);
        }else{
            return $this->error("会员申请记录失败");
        }

    }


    public function rule(){
        return $this->fetch();
    }
    public function paysuccess(){
        return $this->fetch();
    }

    //个人中心 我的培训
    public function train(){
        $userId = session('userId');
        $left = CourseApply::where('userid',$userId)->where('status',1)->where('end_time','gt',time())->order('id desc')->limit(6)->select();//未结束
        foreach($left as $k=>$v){
//            $left[$k]['course_name'] = Course::where('id',$v['course_id'])->value('course_name');
            $q = VenueCourse::where('id',$v['course_id'])->value('content');
            $a = str_replace('&nbsp;','',strip_tags($q));
            $z = str_replace(" ",'',$a);
            $left[$k]['content'] = str_replace("\n",'',$z);
            $img = VenueCourse::where('id',$v['course_id'])->value('front_cover');
            $left[$k]['front_cover'] = $img;
        }
        $right = CourseApply::where('userid',$userId)->where('status',1)->where('end_time','elt',time())->order('id desc')->limit(6)->select();//已结束
        foreach($right as $key=>$value){
//            $right[$key]['course_name'] = Course::where('id',$value['course_id'])->value('course_name');
            $qq= VenueCourse::where('id',$value['course_id'])->value('content');
            $aa = str_replace('&nbsp;','',strip_tags($qq));
            $zz = str_replace(" ",'',$aa);
            $right[$key]['content2'] = str_replace("\n",'',$zz);
            $img2 = VenueCourse::where('id',$value['course_id'])->value('front_cover');
            $right[$key]['front_cover'] = $img2;
        }
        $this->assign('left',$left);
        $this->assign('right',$right);

        return $this->fetch();
    }

    //个人中心  我的培训   上拉加载
    public function more2(){
        $len = input('len');
        $type = input('type');
        $userId = session('userId');

        if($type == 1){
            $info = CourseApply::where('userid',$userId)->where('status',1)->where('end_time','gt',time())->order('id desc')->limit($len,6)->select();//未结束
        }elseif($type == 2){
            $info = CourseApply::where('userid',$userId)->where('status',1)->where('end_time','elt',time())->order('id desc')->limit($len,6)->select();//已结束
        }

        foreach($info as $value){
//            $value['course_name'] = Course::where('id',$value['course_id'])->value('course_name');
            $value['start_time'] = date("Y-m-d",$value['start_time']);
            $value['end_time'] = date("Y-m-d",$value['end_time']);
            $q = VenueCourse::where('id',$value['course_id'])->value('content');
            $a = str_replace('&nbsp;','',strip_tags($q));
            $z = str_replace(" ",'',$a);
            $value['content'] = str_replace("\n",'',$z);
            $img2 = VenueCourse::where('id',$value['course_id'])->value('front_cover');
            $value['front_cover'] = $img2;
//            $value['front_cover'] = Course::where('id',$value['course_id'])->value('front_cover');
            $img = Picture::get($value['front_cover']);
            $value['front_cover'] = $img['path'];
        }

        if($info){
            return $this->success("加载成功",'',$info);
        }else{
            return $this->error("加载失败");
        }
    }

    //个人中心  我的比赛
    public function play(){
        $userId = session('userId');
        $left = CompetitionApply::where('userid',$userId)->where('status',1)->where('end_time','gt',time())->order('id desc')->limit(6)->select();//报名未结束

        foreach($left as $k=>$v){
            $left[$k]['front_cover'] = Competition::where('id',$v['competition_id'])->value('front_cover');
        }

        $right = CompetitionApply::where('userid',$userId)->where('status',1)->where('end_time','elt',time())->order('id desc')->limit(6)->select();//报名已结束

        foreach($right as $key=>$value){
            $right[$key]['front_cover'] = Competition::where('id',$value['competition_id'])->value('front_cover');
        }
        $this->assign('left',$left);
        $this->assign('right',$right);

        return $this->fetch();
    }

    //个人中心  我的比赛   上拉加载
    public function more(){
        $len = input('len');
        $type = input('type');
        $userId = session('userId');

        if($type == 1){
            $info = CompetitionApply::where('userid',$userId)->where('status',1)->where('end_time','gt',time())->order('id desc')->limit($len,6)->select();//报名未结束
        }elseif($type == 2){
            $info = CompetitionApply::where('userid',$userId)->where('status',1)->where('end_time','elt',time())->order('id desc')->limit($len,6)->select();//报名已结束
        }

        foreach($info as $value){
            $value['end_time'] = date("Y-m-d",$value['end_time']);
            $value['front_cover'] = Competition::where('id',$value['competition_id'])->value('front_cover');
            $img = Picture::get($value['front_cover']);
            $value['front_cover'] = $img['path'];
        }

        if($info){
            return $this->success("加载成功",'',$info);
        }else{
            return $this->error("加载失败");
        }
    }



    //个人中心  请假申请(列表首页)
    public function leave(){
        $type = input('type');
        $this->assign('type',$type);
        $userId = session('userId');
        $user = WechatUser::where('userid',$userId)->find();
        $this->assign('user',$user);
        if($type == 1){
            $data = Apply::where('create_user',$userId)->order('id desc')/*->limit(6)*/->select();
            $this->assign('data',$data);
        }else{
            //待审核
            $map = array('leave' => array('eq',$userId),'leavezt' => array('eq',0));
            $q = Apply::where($map);
            $left = $q->whereOr(function($q)use($userId){
                $map2 = array('leavetwo' => array('eq',$userId),'leavetwozt' => array('eq',0),'leavezt' => array('eq',1));
                $q->where($map2);
            })->order('id desc')/*->limit(6)*/->select();//待审核
//            dump($left->fetchSql()->select());exit;////sql语句查询
            //已审核
            $m = array('leave' => array('eq',$userId),'leavezt' => array('neq',0));
            $b = Apply::where($m);
            $right = $b->whereOr(function($b)use($userId){
                $m2 = array('leavetwo' => array('eq',$userId),'leavetwozt' => array('neq',0));
                $b->where($m2);
            })->order('id desc')/*->limit(6)*/->select();//已审核
            $this->assign('left',$left);
            $this->assign('right',$right);
        }

        return $this->fetch();
    }

    public function leavedetail(){
        //判断身份
        $userId = session('userId');
        $user = WechatUser::where('userid',$userId)->find();
        $this->assign('user',$user);
        //获取该请假信息
        $Id = input('id');
        $data = Apply::where('id',$Id)->find();
//        dump($userId);dump($data['leave']);dump($data['leavetwo']);exit;
        if($userId == $data['create_user']){
            $spk = 0;
        }else{
            $spk = 1;
        }
        $this->assign('spk',$spk);
        //请假人姓名
        $u = WechatUser::where('userid',$data['create_user'])->find();
        $data['create_user'] = $u['name'];
        //一级审核人姓名
        if(!empty($data['leave'])){
            $a = WechatUser::where('userid',$data['leave'])->find();
            $data['leave'] = $a['name'];
            if(!empty($a['header'])){
                $data['img'] = $a['header'];
            }else{
                $data['img'] = '/home/images/common/vistor.jpg';
            }
        }
        //二级审核人姓名
        if(!empty($data['leavetwo'])){
            $b = WechatUser::where('userid',$data['leavetwo'])->find();
            $data['leavetwo'] = $b['name'];
            $data['img2'] = ($b['header']) ? $b['header'] : '/home/images/common/vistor.jpg';
        }
        //图片
        if(!empty($data['front_cover'])){
            $data['front_cover'] = json_decode($data['front_cover']);
        }
        $this->assign('data',$data);
        //判断审批框是审批模块进入还是请假模块进入的显示


        //判断时候已经审批,审批框是否显示
        $li = Apply::where('id',$Id)->find();
        if($userId == $li['leave']){
            if($data['leavezt'] == 0){
                $sp = 1;//显示
            }else{
                $sp = 0;//不显示
            }
        }else{
            if($data['leavetwozt'] == 0){
                $sp = 1;//显示
            }else{
                $sp = 0;//不显示
            }
        }
        $this->assign('sp',$sp);

        return $this->fetch();
    }

    //审批提交 意见
    public function view(){
        $data = input('post.');
        $userId = session('userId');
        $list = Apply::where('id',$data['id'])->find();
        if($list['leave'] == $userId){
            $da['id'] = $data['id'];
            $da['leavetext'] = $data['leavetext'];
            $da['leavetime'] = time();
            $da['leavezt'] = $data['status'];
            if($data['status'] == 2){
                $da['status'] = $data['status'];
            }elseif($data['status'] == 1){
                if(empty($list['leavetwo'])){
                    $da['status'] = $data['status'];
                }
            }
            $info = Apply::update($da);
            if($info) {
                //审核后推送
                $a = Apply::where('id',$data['id'])->find();
                if($a['leavezt'] == 1) {
                    if (!empty($a['leavetwo'])) {
                        $yh = $a['leavetwo'];
                        $tsr = $a['create_user'];
                        $this->push($yh, $tsr);
                    }else{
                        //审核成功   抄送推送
                        $cs = WechatUser::where('userid',$a['create_user'])->field('push1,push2,push3')->find();
                        $name = WechatUser::where('userid',$a['create_user'])->value('name');
                        $this->push2($cs,$name);
                        //审核后通过推送申请人
                        $zt = '通过';
                        $yh = $a['create_user'];
                        $this->push3($zt,$yh);
                    }
                }else{
                    $zt = '否决';
                    $yh = $a['create_user'];
                    $this->push3($zt,$yh);
                }
                return $this->success("审批成功");
            }else{
                return $this->error("审批失败");
            }
        }elseif($list['leavetwo'] == $userId){
            $da['id'] = $data['id'];
            $da['leavetwotext'] = $data['leavetext'];
            $da['leavetwotime'] = time();
            $da['leavetwozt'] = $data['status'];
            $da['status'] = $data['status'];
            $info = Apply::update($da);
            if($info) {
                //2级审核后推送
                $aa = Apply::where('id',$data['id'])->find();
                if($aa['leavetwozt'] == 1) {
                        //审核成功   抄送推送
                        $cs = WechatUser::where('userid',$aa['create_user'])->field('push1,push2,push3')->find();
                        $name = WechatUser::where('userid',$aa['create_user'])->value('name');
                        $this->push2($cs,$name);
                       //审核后通过推送申请人
                        $zt = '通过';
                        $yh = $aa['create_user'];
                        $this->push3($zt,$yh);
                    }else{
                        $zt = '否决';
                        $yh = $aa['create_user'];
                        $this->push3($zt,$yh);
                }

                return $this->success("审批成功");
            }else{
                return $this->error("审批失败");
            }
        }

    }

    //个人中心  请假申请(申请页面)
    public function application(){
        $userId = session('userId');
        $user = WechatUser::where('userid',$userId)->find();
        $this->assign('user',$user);
        if(!empty($user['telephone'])){
            $name1 = WechatUser::where('userid',$user['telephone'])->find();
            $data['name1'] = $name1['name'];
            $data['img1'] = ($name1['header']) ? $name1['header'] : '/home/images/common/vistor.jpg';
        }else{
            $data['name1'] = '';
            $data['img1'] = '';
        }
        if(!empty($user['telephone2'])){
            $name2 = WechatUser::where('userid',$user['telephone2'])->find();
            $data['name2'] = $name2['name'];
            $data['img2'] = ($name2['header']) ? $name2['header'] : '/home/images/common/vistor.jpg';
        }else{
            $data['name2'] = '';
            $data['img2'] = '';
        }
        $this->assign('data',$data);

        return $this->fetch();
    }

    //个人中心  请假申请 提交数据库
    public function apply(){
        $userId = session('userId');
        $data = input('post.');
        $user = WechatUser::where('userid',$userId)->find();

        if(!empty($user['telephone'])){
            $data['leave'] = $user['telephone'];
        }//一级审批者
        if(!empty($user['telephone2'])){
            $data['leavetwo'] = $user['telephone2'];
        }//二级审批者
        if(!empty($data['front_cover'])){
            $data['front_cover'] = json_encode($data['front_cover']);
        }//图片json
        $data['create_user'] = $userId;
        $data['create_time'] = time();
        $info = Apply::create($data);

        if($info) {
            if($user['tag'] == 1){
                $start = strtotime($data['starttime']);
                $end = strtotime($data['endtime']);
                $cr = new ClassRecord();
                $yh = $cr->getCoach($start,$end,$userId);
                if(!empty($yh)){
                    $this->push4($user['name'],$data['starttime'],$data['endtime'],$yh);//抄送课程教练
                }

            }else{
                if(!empty($user['telephone'])){
                $yh = $user['telephone'];
                $tsr = '';
                $this->push($yh,$tsr);
                }//一级审批者非空
            }
            return $this->success("提交成功");
        }else{
            return $this->error("提交失败");
        }
    }

    //个人中心  我的比赛  已报名的退赛
    public function reite(){
        $Id = input('id');
        $data = CompetitionApply::where('id',$Id)->find();
        if ($data['type'] == 1) {
            $data['type'] = '个人赛';
        } elseif ($data['type'] == 2)  {
            $data['type'] = '团队赛';
        }
        if ($data['kinds'] == 1) {
            $data['kinds'] = '花剑';
        } elseif ($data['kinds'] == 2)  {
            $data['kinds'] = '重剑';
        }elseif ($data['kinds'] == 3) {
            $data['kinds'] = '佩剑';
        }
        $this->assign('data',$data);

        return $this->fetch();
    }

    //申请退赛
    public function retire(){
        $id = input('id');
        $map['state'] = 1;
        $info = CompetitionApply::where('id',$id)->update($map);

        if($info){
            return $this->success("申请成功");
        }else{
            return $this->error("申请失败");
        }
    }


    public function reite01(){
        return $this->fetch();
    }

    //教练.管理员请假推送
    public function push($yh,$tsr){
        $httpUrl = config('http_url');
        $date = date("Y年n月j日");

        $pre1 = "【请假申请】";
        if(!empty($tsr)){
            $name = WechatUser::where('userid',$tsr)->value('name');
        }else{
            $userId = session('userId');
            $name = WechatUser::where('userid',$userId)->value('name');
        }
        $xx = '您当前有一条来自【'.$name.'】的请假调休申请,请及时审批,谢谢.';

        //发送给企业号
        $send = array(
            "title" => $pre1,
            "description" => $xx,
            "url" => $httpUrl."/home/user/leave/?type=2",
        );
        $Wechat = new TPQYWechat(Config::get('user'));
        $newsConf = config('user');

        $message = array(
            "msgtype" => 'textcard',
            "agentid" => $newsConf['agentid'],
            "textcard" => $send,
            "safe" => "0"
        );
        $message['touser'] = $yh;

        return $Wechat->sendMessage($message);
    }

    //教练.管理员请假审核成功后(抄送push1.push2.push3)
    public function push2($cs,$name){
        $pre1 = "【请假申请抄送】";
        $xx = '您当前有一条来自【'.$name.'】的请假调休申请抄送,请您知悉,谢谢.';

        //发送给企业号
        $send = array(
            "content" => $pre1.$xx
        );
        $Wechat = new TPQYWechat(Config::get('user'));
        $newsConf = config('user');

        $message = array(
            "msgtype" => 'text',
            "agentid" => $newsConf['agentid'],
            "text" => $send,
            "safe" => "0"
        );
        $ps = join('|', json_decode($cs, true));
        $message['touser'] = $ps;

        return $Wechat->sendMessage($message);
    }

    //教练.管理员请假审核成功后(抄送push1.push2.push3)
    public function push3($zt,$yh){
        $xx = '您好,您的请假调休申请已被领导'.$zt.',请您知悉.';

        //发送给企业号
        $send = array(
            "content" => $xx
        );
        $Wechat = new TPQYWechat(Config::get('user'));
        $newsConf = config('user');

        $message = array(
            "msgtype" => 'text',
            "agentid" => $newsConf['agentid'],
            "text" => $send,
            "safe" => "0"
        );
        $message['touser'] = $yh;

        return $Wechat->sendMessage($message);
    }

    //学员请假(直接抄送相关课程教练)
    public function push4($name,$start,$end,$yh){
        $xx = '您好,'.$name.'小朋友申请于'.$start.' - '.$end.'期间请假,请您知悉.';

        //发送给企业号
        $send = array(
            "content" => $xx
        );
        $Wechat = new TPQYWechat(Config::get('user'));
        $newsConf = config('user');

        $message = array(
            "msgtype" => 'text',
            "agentid" => $newsConf['agentid'],
            "text" => $send,
            "safe" => "0"
        );
        $cs = array_unique($yh);

        foreach($cs as $v){
            $message['touser'] = $v;

            $Wechat->sendMessage($message);
        }
    }

}