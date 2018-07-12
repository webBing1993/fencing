<?php
/**
 * Created by PhpStorm.
 * User: 王泽锋
 * Date: 2018/07/12
 * Time: 14:10
 */

namespace app\admin\model;

use think\Model;

class Notice extends Base {
    public $insert = [
        'likes' => 0,
        'views' => 0,
        'comments' => 0,
        'create_time' => NOW_TIME,
    ];

    //获取后台用户名称
    public function user(){
        return $this->hasOne('Member','id','create_user');
    }
    
    public function name(){
        return $this->hasOne('WechatUser','userid','userid');
    }
    //推送
    public function push($info,$idArr=[]) {
        if(empty($idArr)) {
            $arr = $this ->where($info)->select();
        } elseif(!empty($idArr) && $idArr[0] == 3) {
            $arr = $this ->where($info) ->where(['id'=>['neq',$idArr[1]]])->select();
        } else {
            $arr = $this ->where($info)->select();
        }

        foreach($arr as  $value){
            $value['title'] = '【通知公告】'.$value['title'];
            $value['id'] = '3-'.$value->id;
        }
        return $arr;
    }
}