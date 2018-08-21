<?php
/**
 * Created by PhpStorm.
 * User: 王泽锋
 * Date: 2018/07/18
 * Time: 09:53
 */

namespace app\admin\model;
use think\Model;

class CourseUser extends Base {
    public $insert = [
        'create_time' => NOW_TIME,
    ];

    //获取后台用户名称
    public function user(){
        return $this->hasOne('Member','id','create_user');
    }

}
