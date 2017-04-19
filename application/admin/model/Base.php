<?php
/**
 * Created by PhpStorm.
 * User: 虚空之翼 <183700295@qq.com>
 * Date: 16/5/20
 * Time: 10:11
 */

namespace app\admin\model;

use think\Model;

class Base extends Model
{
    protected $autoWriteTimestamp = false;

    //获取新增用户名称
    public function cuser(){
        return $this->hasOne('Member','id','create_user');
    }

    //获取更新用户
    public function uuser(){
        return $this->hasOne('Member','id','update_user');
    }
}