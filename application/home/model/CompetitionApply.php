<?php
/**
 * Created by PhpStorm.
 * User: 王泽锋
 * Date: 2018/07/19
 * Time: 09:22
 */

namespace app\home\model;

use think\Model;

class CompetitionApply extends Model {
    public $insert = [
        'create_time' => NOW_TIME,
    ];

    //获取后台用户名称
    public function user(){
        return $this->hasOne('Member','id','create_user');
    }
}