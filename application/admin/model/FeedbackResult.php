<?php
/**
 * Created by PhpStorm.
 * User: 老王
 * Date: 2017/3/28
 * Time: 15:30
 */
namespace app\admin\model;
use think\Model;
class FeedbackResult extends Model{
    protected $insert = [
        'create_time' => NOW_TIME
    ];
}