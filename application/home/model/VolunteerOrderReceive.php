<?php
/**
 * Created by PhpStorm.
 * User: Lxx<779219930@qq.com>
 * Date: 2017/2/8
 * Time: 15:35
 */

namespace app\home\model;


use think\Model;

class VolunteerOrderReceive extends Model {
    protected $insert = [
        'create_time' => NOW_TIME,
        'status' => 1,
    ];
}