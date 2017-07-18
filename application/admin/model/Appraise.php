<?php
namespace app\admin\model;
use think\Model;

/**
 * Created by PhpStorm.
 * User: 老王
 * Date: 2017/7/18
 * Time: 9:17
 */

class Appraise extends Model
{
    protected $insert = [
        'create_time' => NOW_TIME,
        'create_user' => UID
    ];
    protected $update = [
        'update_time' => NOW_TIME,
        'update_user' => UID
    ];
    public function user(){
        return $this->hasOne('Member','id','create_user');
    }
    // 获取选项数量
    public function get_num(){
        $id = $this->getData('id');
        $num=AppraiseOptions::where(['app_id' => $id,'status' => 0])->count();
        if($num){
            return $num;
        }else{
            return 0;
        }
    }
}