<?php
/**
 * Created by PhpStorm.
 * User: Lxx<779219930@qq.com>
 * Date: 2016/4/25
 * Time: 17:35
 */

namespace app\home\model;
use think\Model;

class Sign extends Model {

    public static function addSign($type, $table, $pid, $userId, $user_name, $openid, $venue_id, $class_id, $member_type, $mold, $date, $current_time)
    {
        $data['type'] = $type;
        $data['table'] = $table;
        $data['pid'] = $pid;
        $data['userid'] = $userId;
        $data['name'] = $user_name;
        $data['openid'] = $openid;
        $data['venue_id'] = $venue_id;
        $data['class_id'] = $class_id;
        $data['member_type'] = $member_type;
        $data['mold'] = $mold;
        $data['date'] = $date;
        $data['create_time'] = $current_time;
        $model = Sign::create($data);
        if ($model) {
            return $model;
        } else {
            return false;
        }
    }
}