<?php
/**
 * Created by PhpStorm.
 * User: Lxx<779219930@qq.com>
 * Date: 2017/2/27
 * Time: 10:41
 */

namespace app\admin\model;


class Redpush extends Base {
    protected $insert = [
        'create_time' => NOW_TIME,
        'status' => 0,
    ];
}