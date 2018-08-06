<?php
/**
 * Created by PhpStorm.
 * User: 王泽锋
 * Date: 2018/07/17
 * Time: 09:33
 */

namespace app\admin\validate;

use think\Validate;

class CompetitionApply extends Validate {
    protected $rule = [
        'competition_id'  =>  'require',
        'userid' =>  'require',
        'title'  =>  'require',
        'end_time'  =>  'require',
        'group_id' =>  'require',
        'group_name'  =>  'require',
        'event_id'  =>  'require',
        'type' =>  'require',
        'kinds'  =>  'require',
        'representative'  =>  'require',
        'coach' =>  'require',
        'card_type'  =>  'require',
        'card_num'  =>  'require',
        'price' =>  'require',
    ];

    protected $message = [
        'competition_id.require'  =>  '参数缺失！',
        'userid.require' =>  '参数缺失！',
        'title.require' =>  '参数缺失！',
        'end_time.require'  =>  '参数缺失！',
        'group_id.require' =>  '请选择组别！',
        'group_name.require' =>  '参数缺失！',
        'event_id.require'  =>  '参数缺失！',
        'type.require' =>  '请选择赛别！',
        'kinds.require' =>  '请选择剑种！',
        'representative.require'  =>  '请填写代表单位！',
        'coach.require' =>  '请填写带训教练！',
        'card_type.require' =>  '请选择证件类型！',
        'card_num.require'  =>  '请填写证件号！',
        'price.require' =>  '参数缺失！',
    ];

}