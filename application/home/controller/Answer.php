<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/18
 * Time: 15:57
 */
namespace app\home\controller;

use app\home\model\Notice;
use think\Db;
use com\wechat\TPQYWechat;
use think\Config;
use app\home\model\Picture;

class Answer extends Base{
    /**
     * @return mixed  主页
     */
    public function index(){
        return $this->fetch();
    }
}