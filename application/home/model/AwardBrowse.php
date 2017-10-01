<?php
/**
 * Created by PhpStorm.
 * User: laowang <958364865@qq.com>
 * Date: 2017/9/26
 * Time: 16:28
 */

namespace app\home\model;
use think\Model;
class AwardBrowse extends Model
{
    protected $insert = [
        'create_time' => NOW_TIME,
        'score' => 2,
    ];
}