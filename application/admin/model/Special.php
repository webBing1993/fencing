<?php
/**
 * Created by PhpStorm.
 * User: 老王
 * Date: 2017/5/10
 * Time: 10:05
 */
namespace app\admin\model;
use think\Model;
class Special extends Model{
    protected $insert = [
        'likes' => 0,
        'views' => 0,
        'comments' => 0,
        'create_time' => NOW_TIME,
    ];
    //获取后台用户名称
    public function user(){
        return $this->hasOne('Member','id','create_user');
    }
}