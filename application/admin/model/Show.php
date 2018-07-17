<?php
/**
 * Created by PhpStorm.
 * User: 王泽锋
 * Date: 2018/07/17
 * Time: 13:57
 */

namespace app\admin\model;

use think\Model;

class Show extends Base {
    public $insert = [
//        'views' => 0,
//        'likes' => 0,
//        'comments' => 0,
        'create_time' => NOW_TIME,
    ];

    //获取后台用户名称
    public function user(){
        return $this->hasOne('Member','id','create_user');
    }

}