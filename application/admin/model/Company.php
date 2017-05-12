<?php
/**
 * Created by PhpStorm.
 * User: 老王
 * Date: 2017/5/11
 * Time: 15:35
 */
namespace app\admin\model;
use think\Model;
class Company extends Model{
    protected $insert = [
        'views' => 0,
        'comments' => 0,
        'likes' => 0,
        'create_time' => NOW_TIME,
    ];
    //获取后台用户名称
    public function user(){
        return $this->hasOne('Member','id','create_user');
    }
}