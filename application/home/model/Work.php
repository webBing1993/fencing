<?php
namespace app\home\model;
use think\Model;
use app\home\model\Picture;
/**
 * Created by PhpStorm.
 * User: 老王
 * Date: 2017/7/17
 * Time: 17:59
 */
class Work extends Model
{
    /**
     * 加载更多
     */
    //首页获取已推送的数据
    public function get_list($where,$length=0){
        $list = $this->where($where)->order('create_time','desc')->limit($length,10)->select();
        foreach($list as $value){
            //$value['create_time'] = date('Y-m-d',$value['create_time']);
            //$value['meet_endtime'] = strtotime($value['meet_endtime']);
            $value['front_cover'] = Picture::where('id',$value['front_cover'])->value('path');
        }
        return $list;
    }
}