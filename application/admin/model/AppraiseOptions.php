<?php
namespace app\admin\model;
use think\Model;

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/7/18
 * Time: 10:06
 */

class AppraiseOptions extends Model
{
    public $insert = [
        'num' => 0,
        'status' => 0,
        'create_time' => NOW_TIME,
        'create_user' => UID
    ];
    protected $update = [
        'update_time' => NOW_TIME,
        'update_user' => UID
    ];
    public function user(){
        return $this->hasOne("Member",'id','create_user');
    }
    // 获取 所属主题
    public function getTitle(){
        return $this->hasOne('Appraise','id','app_id');
    }
}