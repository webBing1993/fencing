<?php
/**
 * Created by PhpStorm.
 * User: Lxx<779219930@qq.com>
 * Date: 2016/4/25
 * Time: 17:35
 */

namespace app\home\model;
use think\Model;

class ClassRecord extends Model {

    public static function getCoach($start, $end, $userId){
        $class_id = ClassRecord::where(['start_time' => ['>=', $start], 'end_time' => ['<=', $end], 'userid' => $userId, 'status' => 0])->column('class_id');
        $userid_arr = ClassRecord::where(['class_id' => ['in', $class_id], 'member_type' => ['<>', 2], 'status' => 0])->group('userid')->column('userid');
        return $userid_arr;
    }
}