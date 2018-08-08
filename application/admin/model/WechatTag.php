<?php
/**
 * Created by PhpStorm.
 * User: Lxx<779219930@qq.com>
 * Date: 2016/5/20
 * Time: 16:03
 */

namespace app\admin\model;


use think\Model;

class WechatTag extends Base {

    const TAG_1 = 1;
    const TAG_2 = 2;
    const TAG_3 = 3;
    const TAG_4 = 4;
    const TAG_5 = 5;
    const TAG_6 = 6;
    const TAG_7 = 7;
    const TAG_8 = 8;
    const TAG_ARRAY = [
        self::TAG_1 => 4,//超级管理员
        self::TAG_2 => 4,//子管理员
        self::TAG_3 => 3,//区域管理员
        self::TAG_4 => 2,//场馆负责人
        self::TAG_5 => 2,//主教练
        self::TAG_6 => 2,//教练
        self::TAG_7 => 2,//工作人员
        self::TAG_8 => 1,//学员
    ];
    public function Wechat_user() {
        return $this->belongsToMany('WechatUser','WechatTag');
    }
}