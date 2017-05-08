<?php
/**
 * Created by PhpStorm.
 * User: Lxx<779219930@qq.com>
 * Date: 2017/1/11
 * Time: 9:40
 */

namespace app\admin\model;


class Redbook extends Base {
    protected $insert = [
        'create_time' => NOW_TIME,
        'have_read' => 0,
        'comments' => 0,
        'likes' => 0,
        'views' => 0,
        'status' => 1,
    ];

    //获取后台用户名称
    public function user(){
        return $this->hasOne('Member','id','create_user');
    }

}