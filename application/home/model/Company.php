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
    //首页获取已推送的数据
    public function get_list($where,$length=0){
        $list = $this->where($where)->order('create_time','desc')->limit($length,10)->select();
        foreach($list as $value){
            $value['create_time'] = date('Y-m-d',$value['create_time']);
            $value['image'] = Picture::where('id',$value['image'])->value('path');
        }
        return $list;
    }

    /**
     * 首页获取推荐的数据
     * @param $length
     * @param string $push 推送数据获取
     */
    public function getDataList($length,$status=0,$type=0){
        if ($status == 0){
            $map = array(
                'status' => 0,
                'type' => $type
            );
        }else{
            $map = array(
                'userid' => ['neq',''],
                'status' => ['egt',$status],
                'type' => $type
            );
        }
        $order = 'create_time desc';
        $limit = "$length,4";
        $list = $this ->where($map) ->order($order) ->limit($limit) ->select();
        if(!empty($list))
        {
            return $list[0] ->data;
        }else{
            return $list;
        }
    }
    //获取后台用户名称
    public function user(){
        return $this->hasOne('Member','id','create_user');
    }

    public function name(){
        return $this->hasOne('WechatUser','userid','userid');
    }
}