<?php
/**
 * Created by PhpStorm.
 * User: 老王
 * Date: 2017/3/8
 * Time: 14:57
 */
namespace app\home\model;
use think\Model;
class VoteAnswer extends Model{
    protected $insert = [
        'create_time' => NOW_TIME
    ];
}