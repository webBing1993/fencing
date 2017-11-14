<?php
/**
 * Created by PhpStorm.
 * User: 老王
 * Date: 2016/11/2
 * Time: 13:21
 */
namespace app\home\controller;
use app\home\model\WechatUser;

class Structure extends Base{
    /*
     * 组织架构主页
     */
    public function index(){
        $msg = $this ->statistics();
        $this ->assign('msg',$msg);
        return $this->fetch();
    }
    /*
     * 组织架构详情页
     */
    public function detail(){
        $party = input('party');
        $this->assign('party',$party);
        return $this->fetch();
    }
}
