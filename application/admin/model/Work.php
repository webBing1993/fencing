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
    // 获取  部门
    public function department(){
        $branch = $this->getData('branch');
        $list = WechatDepartment::where(['id' =>$branch])->field('name')->find();
        return $list['name'];
    }
    // 获取 姓名
    public function name(){
        $userid = $this->getData('publisher');
        $user = WechatUser::where('userid',$userid)->field('name')->find();
        return $user['name'];
    }
}