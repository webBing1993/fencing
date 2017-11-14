<?php
/**
 * Created by PhpStorm.
 * User: Lxx<779219930@qq.com>
 * Date: 2016/9/12
 * Time: 16:12
 */

namespace app\home\controller;
use app\home\model\Browse;
use app\home\model\Comment;
use app\home\model\Like;
use app\home\model\Picture;
use app\home\model\WechatUser;
use think\Controller;

use app\home\model\Notice as NoticeModel;
use think\Db;

/**
 * Class Notice
 * @package  鸡毛传帖
 */
class Notice extends Base {
    /**
     * 主页
     */
    public function index(){
        $this->anonymous(); //判断是否是游客
        return $this->fetch();
    }

    /**
     * 更多  通知
     */
    public function leadlistmore(){
        $len = input('length');
        $type = input('type');
    }
    /**
     *  相关通知  活动通知 详细页
     */
    public function forumnotice(){
    }

}