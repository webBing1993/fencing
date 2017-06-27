<?php
/**
 * Created by PhpStorm.
 * User: 老王
 * Date: 2017/1/13
 * Time: 13:04
 */
namespace app\admin\model;
use think\Model;
class Vote extends Model{

    public $insert = [
        'views' => 0,
        'create_time' => NOW_TIME
    ];
    public function user(){
        return $this->hasOne("Member",'id','create_user');
    }
    // 获取选项数量
    public function get_num(){
        $id = $this->getData('id');
        $num=VoteOptions::where('vote_id',$id)->count();
        if($num){
            return $num;
        }else{
            return 0;
        }
    }
}