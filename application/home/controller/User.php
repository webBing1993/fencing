<?php
/**
 * Created by PhpStorm.
 * User: Lxx<779219930@qq.com>
 * Date: 2016/4/20
 * Time: 15:34
 */

namespace app\home\controller;
use app\home\model\Picture;
use app\home\model\WechatDepartment;
use app\home\model\WechatDepartmentUser;
use app\home\model\WechatUser;
use app\home\model\WechatUserTag;
use think\Db;
use app\home\model\Notice;
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
        //dump($userId);exit();
        $header = input('header');
        //dump($header);exit();
        $map = array(
            'header' => $header,
        );
        $info = WechatUser::where('userid',$userId)->update($map);
        //dump($info);exit();
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
        $this->assign('user',$user);
        return $this->fetch();
    }
    /**
     * 我的签到
     */
    public function checkin() {
        $userId = session('userId');
        $user = WechatUser::where('userid',$userId)->find();
        $this->assign('img',$user['avatar']);
        $this->assign('id',$userId);
        return $this->fetch();
    }

    /**
     * 会议纪要
     */
    public function meeting() {
        $userId = session('userId');
        $map = ['type' => 3 ,'status' => ['egt',0] , 'userid' => $userId];
        $list = Db::name('notice')->where($map)->order('create_time','desc')->limit(12)->select();
        $this->assign('meeting',$list);
        return $this->fetch();
    }
    /**
     * 会议 更多
     */
    public function more(){
        $len = input('length');
        $userId = session('userId');
        $map = ['type' => 3 ,'status' => ['egt',0] , 'userid' => $userId];
        $list = Db::name('notice')->where($map)->order('create_time','desc')->limit($len,12)->select();
        foreach($list as $key => $value){
            $list[$key]['time'] = date('Y-m-d',$value['create_time']);
            $list[$key]['path'] = Picture::where('id',$value['front_cover'])->value('path');
        }
        if ($list){
            return $this->success('加载成功','',$list);
        }else{
            return $this->error('加载失败');
        }
    }
    /**
     * 会议详情
     */
    public function meetingdetail(){
        $this->jssdk();
        $id = input('get.id/d');
        $notice = new Notice();
        $info = $notice->where('id',$id)->find();
        $info['images']=json_decode($info['images'],true);
        $this->assign('detail',$info);
        return $this->fetch();
    }
}
