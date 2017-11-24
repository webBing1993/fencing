<?php
/**
 * Created by PhpStorm.
 * User: stiff <1570004138@163.com>
 * Date: 2017/5/24
 * Time: 9:22
 */
namespace app\home\controller;
use think\Controller;
use app\user\controller\Index as APIIndex;
use com\wechat\TPQYWechat;

class Verify extends Controller{
    /**
     * 用户登入获取信息
     */
    public function login(){
        // 获取用户信息
        $Wechat = new TPQYWechat(config('user'));
        $result = $Wechat->getUserId(input('code'), config('user.agentid'));
        if(isset($result['UserId'])) {
            $user = $Wechat->getUserInfo($result['UserId']);
            $user['department'] = json_encode($user['department']);
            /*
             * array(10) {
                  ["errcode"] => int(0)
                  ["errmsg"] => string(2) "ok"
                  ["userid"] => string(15) ""
                  ["name"] => string(9) ""
                  ["department"] => array(1) {
                    [0] => int(493)
                  }
                  ["mobile"] => string(11) ""
                  ["gender"] => string(1) ""
                  ["avatar"] => string(82) ""
                  ["status"] => int()
                  ["extattr"] => array(1) {
                    ["attrs"] => array(0) {
                    }
                  }
            }
             */
            if (isset($user['extattr'])){
                $user['extattr'] = json_encode($user['extattr']);
            }
            $user['order'] = json_encode($user['order']);
            // 添加本地数据
            $UserAPI = new APIIndex();
            $localUser = $UserAPI->checkWechatUser($result['UserId']);
            if($localUser) {
                $UserAPI->updateWechatUser($user);
            } else {
                $UserAPI->addWechatUser($user);
            }
            session("userId", $result['UserId']);
            //存在url则跳转，不存在则回主页
            if(session('url')){
                $this->redirect(session('url'));
                session('url','');
            }else{
                $this->redirect("Index/index");
            }
        } else {
            // 用户不存在通讯录默认为游客，跳转到url;
            session('userId','visitor');
            $this->redirect(session('url'));
        }
    }
    public function null(){
        return $this->fetch();
    }

}