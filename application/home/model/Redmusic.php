<?php
/**
 * Created by PhpStorm.
 * User: Lxx<779219930@qq.com>
 * Date: 2017/1/10
 * Time: 15:02
 */

namespace app\home\model;


use think\Model;

class Redmusic extends Model {
    /**
     * 获取主页列表
     */
    public function getIndexList() {
        $map = array(
            'status' => 1
        );
        $order = array("create_time desc");
        $res = $this->where($map)->order($order)->limit(10)->select();
        foreach ($res as $value) {
            $value['time'] = date("Y-m-d",$value['create_time']);
            $path = Picture::get($value['front_cover']);
            $value['path'] = $path['path'];
        }
        return $res;
    }
}