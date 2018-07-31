<?php
/**
 * Created by PhpStorm.
 * User: 王泽锋
 * Date: 2018/07/17
 * Time: 09:30
 */

namespace app\admin\model;
use think\Model;

class CompetitionEvent extends Base {
    //比赛类型
    const INDIVIDUAL_EVENT = 1;
    const TEAM_EVENT = 2;
    const EVENT_TYPE_ARRAY = [
        self::INDIVIDUAL_EVENT  => '个人赛',
        self::TEAM_EVENT  => '团队赛',
    ];
    //剑种
    const FOIL_KINDS = 1;
    const DUELLING_KINDS = 2;
    const SABERS_KINDS = 3;
    const EVENT_KINDS_ARRAY = [
        self::FOIL_KINDS  => '花剑',
        self::DUELLING_KINDS  => '重剑',
        self::SABERS_KINDS  => '佩剑',
    ];

    public $insert = [
        'create_time' => NOW_TIME,
    ];

    //获取后台用户名称
    public function user(){
        return $this->hasOne('Member','id','create_user');
    }

}