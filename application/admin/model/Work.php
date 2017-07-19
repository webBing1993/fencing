<?php
namespace app\admin\model;
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/7/19
 * Time: 9:02
 */
use think\Model;
class Work extends Model
{
    public function department(){
        $branch = $this->getData('branch');
        $list = WechatDepartment::where(['id' =>$branch])->field('name')->find();
        return $list['name'];
    }
}