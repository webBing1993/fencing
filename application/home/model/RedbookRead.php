<?php
/**
 * Created by PhpStorm.
 * User: Lxx<779219930@qq.com>
 * Date: 2017/1/12
 * Time: 20:02
 */

namespace app\home\model;


use think\Model;

class RedbookRead extends Model {
    protected $insert = [
        'create_time' => NOW_TIME,
        'status' => 0
    ];
}