<?php
/**
 * Created by PhpStorm.
 * User: Lxx<779219930@qq.com>
 * Date: 2016/4/20
 * Time: 15:34
 */

namespace app\home\controller;
use app\home\model\News;
use app\home\model\Notice;
use app\home\model\Browse;
use app\home\model\Feedback;
use app\home\model\WechatDepartment;
use app\home\model\WechatDepartmentUser;
use app\home\model\WechatUser;
use app\home\model\WechatUserTag;
use think\Controller;
use think\Db;
use com\wechat\TPQYWechat;
use think\Config;
use think\Request;
/**
 * Class User
 * 用户个人中心
 */
class User extends Base {
    /**
     * 个人中心主页
     */
    public function index(){
        //是否游客登录
        $this->anonymous();
        $userId = session('userId');
        $user = WechatUser::where('userid',$userId)->find();
        $this->assign('user',$user);
        //判断是否签到
        if($user){
            //获取当天0点的时间戳
            $timestamp0=strtotime(date('Y-m-d',time()));
            $info = WechatUser::where(['userid' =>$userId,'sign_time' =>['EGT',$timestamp0]])->find();
            //当日未签到为0
            if($info){
                $this ->assign('is_sign',$info['sign']);
            }else{
                $this ->assign('is_sign',0);
            }
        }
        //  是否 具有  游客模式 权限
        $Tourist = WechatUserTag::where(['tagid' => 1, 'userid' => $userId])->find();
        if ($Tourist){
            $this ->assign('tourist',1);
        }else{
            $this ->assign('tourist',0);
        }
        return $this->fetch();
    }

    /**
     * 个人信息页
     */
    public function personal(){
        $userId = session('userId');
        $user = WechatUser::where('userid',$userId)->find();
        switch ($user['gender']) {
            case 0:
                $user['sex'] = "未定义";
                break;
            case 1:
                $user['sex'] = "男";
                break;
            case 2:
                $user['sex'] = "女";
                break;
            default:
                break;
        }
        $Departid = WechatDepartmentUser::where('userid',$userId)->order('id desc')->field('departmentid')->find();
        if (empty($user['branch'])){
             $Depart = WechatDepartment::where('id',$Departid['departmentid'])->find();
            $user['branch'] = $Depart->name;
        }
        $Notice = new Notice();
        $map = array(
            'status' =>array('egt',0)
        );
        $activityAll = $Notice->where($map)->count(); // 相关通知 总数
        $news = News::where(['status' => ['egt',0]])->count();  // 党建动态 
        $Brower = new Browse();
        $map1 = array(
            'user_id' => $userId,
            'notice_id' => array('exp',"is not null")
        );
        $map2 = array(
            'user_id' => $userId,
            'news_id' => array('exp',"is not null")
        );
        $num = $Brower->where($map1)->count(); // 浏览notice总记录
        $num1 = $Brower->where($map2)->count();  // 党建动态  总记录
        $user['activity'] = array(
            'all' => $activityAll,
            'num' => $num,
        );
        $user['news'] = array(
            'all' => $news,
            'num' => $num1,
        );
        $this->assign('user',$user);
        $request = Request::instance() ->domain();
        $this ->assign('request',$request);
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

    /**
     * 临时党员信息
     */
    public function eg() {
        $id = input('id');
        $user = WechatUser::where('userid',$id)->find();
        switch ($user['gender']) {
            case 0:
                $user['sex'] = "未定义";
                break;
            case 1:
                $user['sex'] = "男";
                break;
            case 2:
                $user['sex'] = "女";
                break;
            default:
                break;
        }
        $Departid = WechatDepartmentUser::where('userid',$id)->order('id desc')->field('departmentid')->find();
        if (empty($user['branch'])){
            $Depart = WechatDepartment::where('id',$Departid['departmentid'])->find();
            $user['branch'] = $Depart->name;
        }
        $Notice = new Notice();
        $map = array(
            'status' =>array('egt',0)
        );
        $activityAll = $Notice->where($map)->count(); // 相关通知 总数
        $news = News::where(['status' => ['egt',0]])->count();  // 党建动态 
        $Brower = new Browse();
        $map1 = array(
            'user_id' => $id,
            'notice_id' => array('exp',"is not null")
        );
        $map2 = array(
            'user_id' => $id,
            'news_id' => array('exp',"is not null")
        );
        $num = $Brower->where($map1)->count(); // 浏览notice总记录
        $num1 = $Brower->where($map2)->count();  // 党建动态  总记录
        $user['activity'] = array(
            'all' => $activityAll,
            'num' => $num,
        );
        $user['news'] = array(
            'all' => $news,
            'num' => $num1,
        );
        $this->assign('user',$user);
        return $this->fetch();
    }
    /**
     * 意见反馈
     */
    public function feedback() {
        return $this->fetch();
    }
    /*
     * 意见反馈  提交
     */
    public function feedbackup(){
        $data['content'] = input('post.content');
        $data['userid'] = session('userId');
        $Feedback = new Feedback();
        $res = $Feedback->save($data);
        if ($res){
            return $this->success('提交成功');
        }else{
            return $this->error('提交失败');
        }
    }
    /*
     * 签到功能
     */
    public function sign(){
        $userId = session('userId');
        $week = date('N',time());
        if ($week != 6 && $week != 7){  // 周一 到  周五
            $User = WechatUser::where('userid',$userId)->field('sign,sign_time')->find();
            if ($User['sign'] == 0){
                // 未签到  点击签到
                WechatUser::where('userid',$userId)->update(['sign_time' => time(),'sign' => ['exp','sign+1'],'score' => ['exp','score+1']]);
                return $this->success('签到成功',null,1); // 签到成功   加1分
            }else{
                // 已签到
                $now = date('z',time());  // 当前年份中的第几天
                $time = date('z',$User['sign_time']);  // 签到年份中的第几天
                if ($now - $time >1){
                    // 签到中间有间断  重新计算
                    WechatUser::where('userid',$userId)->update(['sign_time' => time(),'score' => ['exp','score+1'],'sign' => 1]);
                    return $this->success('签到成功',null,1); // 签到成功   加1分
                }else{
                    $score = $User['sign'] + 1;
                    WechatUser::where('userid',$userId)->update(['sign_time' => time(),'sign' => ['exp',"sign+1"],'score' => ['exp',"score+{$score}"]]);
                    return $this->success('签到成功',null,$score);
                }
            }
        }else{
            // 周六 周天
            return $this ->error('周末不给签到,去休息吧!');
        }
    }
}
