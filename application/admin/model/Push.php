<?php
/**
 * Created by PhpStorm.
 * User: 老王
 * Date: 2016/10/23
 * Time: 11:32
 */
namespace app\admin\model;

class Push extends Base{
    public $insert = array(
        'create_time' => NOW_TIME,
        'status' => 0
    );
}