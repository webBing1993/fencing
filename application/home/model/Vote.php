<?php
/**
 * Created by PhpStorm.
 * User: 老王
 * Date: 2017/3/8
 * Time: 13:34
 */
namespace app\home\model;
use think\Model;
class Vote extends Model{
    // 获取该主题下的选项
    public function option(){
        $id = $this->getData('id');
        $list = VoteOptions::where(['vote_id' =>$id,'status' => 0])->order('id desc')->select();
        return $list;
    }
    // 获取该主题下的排名
    public function get_rank(){
        $id = $this->getData('id');
        $list = VoteOptions::where(['vote_id' =>$id,'status' => 0])->order('num desc')->select();
        return $list;
    }
}