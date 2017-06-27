<?php
/**
 * Created by PhpStorm.
 * User: stiff <1570004138@163.com>
 * Date: 2017/5/16
 * Time: 10:12
 */
namespace app\home\model;
use think\Model;

class Company extends Model{
    /**
     * 加载更多
     */
    public function getMoreList($len) {
        $order = array('create_time desc');
        $where = array('status' => array('egt',0));
        $list = $this ->where($where)->order($order)->limit($len,5)->select();
        return $list;
    }
    //  获取 部门 名称
    public function getDepart(){
        return $this->hasOne('WechatDepartment','id','publisher');
    }
}