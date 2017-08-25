<?php
/**
 * Created by PhpStorm.
 * User: Lxx<779219930@qq.com>
 * Date: 2017/1/11
 * Time: 15:19
 */

namespace app\admin\model;


class Redremark extends Base {
    protected $insert = [
        'create_time' => NOW_TIME,
        'status' => 1,
    ];

    //获取后台用户名称
    public function user(){
        return $this->hasOne('Member','id','create_user');
    }
}