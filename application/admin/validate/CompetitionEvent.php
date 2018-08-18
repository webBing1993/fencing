<?php
/**
 * Created by PhpStorm.
 * User: 王泽锋
 * Date: 2018/07/17
 * Time: 09:33
 */

namespace app\admin\validate;

use think\Validate;

class CompetitionEvent extends Validate {
    protected $rule = [
        'type'  =>  'require',
        'kinds' =>  'require',
        'price'  =>  'require|number',
        'vip_price'  =>  'require|number',
    ];

    protected $message = [
        'type.require'  =>  '请添加比赛类型！',
        'kinds.require' =>  '请添加比赛剑种！',
        'price.require'  =>  '请填写普通价！',
        'price.number'  =>  '普通价必须是数字！',
        'vip_price.require'  =>  '请填写会员价！',
        'vip_price.number'  =>  '会员价必须是数字！',
    ];

}