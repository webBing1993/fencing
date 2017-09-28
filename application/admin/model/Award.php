<?php
/**
 * Created by PhpStorm.
 * User: laowang <958364865@qq.com>
 * Date: 2017/9/27
 * Time: 16:01
 */

namespace app\admin\model;
use think\Model;

class Award extends Model
{
//获取后台用户名称
    public function user(){
        return $this->hasOne('WechatUser','userid','userid');
    }
}