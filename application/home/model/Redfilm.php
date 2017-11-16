<?php
/**
 * Created by PhpStorm.
 * User: Lxx<779219930@qq.com>
 * Date: 2017/1/6
 * Time: 10:01
 */

namespace app\home\model;


use think\Model;

class Redfilm extends Model {
    /**
     * 获取主页
     */
    public function getIndexList() {
        $map = array(
            'status' => 1,
            'recommend' => 1,
        );
        $order = array("create_time desc");
        $top = $this->where($map)->order($order)->limit(3)->select();
        unset($map['recommend']);
        $new = $this->where($map)->order($order)->limit(3)->select();
        $order = array("views desc");
        $hot = $this->where($map)->order($order)->limit(3)->select();
        $data = array(
            'top' => $top,
            'new' => $new,
            'hot' => $hot
        );
        return $data;
    }

    /**
     * 获取热播更多列表
     */
    public function getMoreList($len = 0) {
        $map = array(
            'status' => 1,
        );
        $order = array("views desc,create_time desc");
        $res = $this->where($map)->order($order)->limit($len,9)->select();
        foreach ($res as $value) {
            $value['time'] = date("Y-m-d",$value['create_time']);
            $path = Picture::get($value['front_cover']);
            $value['path'] = $path['path'];
        }
        return $res;

    }
}