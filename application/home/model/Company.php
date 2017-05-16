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
    public function getMoreList($data) {
        $order = array('create_time desc');
        $where = array('status' => 0);
        $list = $this ->where($where)->order($order)->limit($data['length'],5)->select();
        return $list;
    }
}