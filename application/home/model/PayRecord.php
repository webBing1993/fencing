<?php
/**
 * Created by PhpStorm.
 * User: 虚空之翼
 * Date: 2018/8/6 下午2:59
 */

namespace app\home\model;

use think\Model;

class PayRecord extends Model {
    const TYPE_1 = 1;
    const TYPE_2 = 2;
    const TYPE_3 = 3;
    const TYPE_4 = 4;
    const TYPE_ARRAY = [
        self::TYPE_1 => 'course_apply',//课程报名
        self::TYPE_2 => 'competition_apply',//比赛报名
        self::TYPE_3 => 3,//升级会员
        self::TYPE_4 => 'shop_order',//商城购买
    ];
}