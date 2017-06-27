<?php
/**
 * Created by PhpStorm.
 * User: 老王
 * Date: 2017/3/7
 * Time: 9:45
 */
namespace app\admin\model;
use think\Model;
class VoteOptions extends Model{
    public $insert = [
        'num' => 0,
        'status' => 0,
        'create_time' => NOW_TIME
    ];
    public function user(){
        return $this->hasOne("Member",'id','create_user');
    }
    // 获取 所属主题
    public function getTitle(){
        return $this->hasOne('Vote','id','vote_id');
    }
}