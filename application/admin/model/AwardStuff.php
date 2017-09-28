<?php
/**
 * Created by PhpStorm.
 * User: laowang <958364865@qq.com>
 * Date: 2017/9/27
 * Time: 13:52
 */

namespace app\admin\model;
use think\Model;
class AwardStuff extends Model
{
    protected $insert = [
        'create_time' => NOW_TIME
    ];
    //获取后台用户名称
    public function user(){
        return $this->hasOne('Member','id','create_user');
    }
}