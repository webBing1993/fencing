<?php
namespace app\home\model;
use think\Model;
/**
 * Created by PhpStorm.
 * User: 老王
 * Date: 2017/7/17
 * Time: 17:59
 */
class Work extends Model
{
    protected $insert = [
        'create_time' => NOW_TIME,
    ];
}