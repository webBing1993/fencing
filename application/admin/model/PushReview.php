<?php
/**
 * Created by PhpStorm.
 * User: 老王
 * Date: 2016/10/23
 * Time: 15:35
 */
namespace app\admin\model;
class PushReview extends Base{
    protected $insert = [
        'review_time' => NOW_TIME,
    ];
}