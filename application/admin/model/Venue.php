<?php
/**
 * Created by PhpStorm.
 * User: 王泽锋
 * Date: 2018/07/16
 * Time: 13:46
 */

namespace app\admin\model;
use think\Model;

class Venue extends Base {
    public $insert = [
        'views' => 0,
        'comments' => 0,
        'create_time' => NOW_TIME,
    ];

    //获取后台用户名称
    public function user(){
        return $this->hasOne('Member','id','create_user');
    }

}