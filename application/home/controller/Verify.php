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
        $Wechat = new TPQYWechat(config('party'));
        $result = $Wechat->getUserId(input('code'), config('party.agentid'));
        if(isset($result['UserId'])) {
            $user = $Wechat->getUserInfo($result['UserId']);

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
}