<?php
/**
 * Created by PhpStorm.
 * User: Lxx<779219930@qq.com>
 * Date: 2016/4/25
 * Time: 17:35
 */

namespace app\home\model;
use think\Model;

class SignStatistics extends Model {

    public static function add($type, $table, $pid, $userId, $user_name, $openid, $venue_id, $member_type, $status, $date)
    {
        $data['type'] = $type;
        $data['table'] = $table;
        $data['pid'] = $pid;
        $data['userid'] = $userId;
        $data['name'] = $user_name;
        $data['openid'] = $openid;
        $data['venue_id'] = $venue_id;
        $data['member_type'] = $member_type;
        $data['status'] = $status;
        $data['date'] = $date;
        $data['create_time'] = time();
        $model = SignStatistics::create($data);
        if ($model) {
            return $model;
        } else {
            return false;
        }
    }
}