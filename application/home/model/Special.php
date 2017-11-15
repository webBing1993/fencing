<?php
namespace app\home\model;
use think\Model;
use app\home\model\Picture;
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/7/18
 * Time: 9:23
 */
class Special extends Model
{
    /**
     * 获取列表数据
     */
    public function get_list($where,$length=0){
        $list = $this->where($where)->order('create_time','desc')->limit($length,10)->select();
        foreach($list as $value){
            $value['create_time'] = date('Y-m-d',$value['create_time']);
            $value['front_cover'] = Picture::where('id',$value['front_cover'])->value('path');
        }
        return $list;
    }
}