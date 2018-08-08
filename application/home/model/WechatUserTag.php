<?php
/**
 * Created by PhpStorm.
 * User: Lxx<779219930@qq.com>
 * Date: 2016/5/23
 * Time: 14:52
 */

namespace app\home\model;


use app\admin\model\WechatTag;
use think\Model;

class WechatUserTag extends Model {

    public function user(){
        return $this->hasOne('WechatUser','userid','userid');
    }

    public static function issetTag($userId, $tagid){
        $rs = WechatUserTag::where(['userid' => $userId, 'tagid' => $tagid])->find();
        if($rs){
            return true;
        }else{
            return false;
        }
    }

    public static function getTagName($tagid){
        return WechatTag::where(['tagid' => $tagid])->value('tagname');
    }

    public static function getVenueId($userId){
        $tagid = WechatUserTag::where(['tagid' => ['>', 9], 'userid' => $userId])->value('tagid');
        if($tagid){
            $tagname = WechatUserTag::getTagName($tagid);
            $venue_id = venue::where(['title' => $tagname, 'status' => ['>=', 0]])->value('id');
            if($venue_id){
                return $venue_id;
            }
            return false;
        }else{
            return false;
        }
    }

}