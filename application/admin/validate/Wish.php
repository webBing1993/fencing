<?php
/**
 * Created by PhpStorm.
 * User: Lxx<779219930@qq.com>
 * Date: 2017/2/8
 * Time: 14:52
 */

namespace app\admin\validate;


use think\Validate;

class Wish extends Validate {
    protected $rule = [
        'title' => 'require',
        'content' => 'require',
        'time' => 'require',
        'address' => 'require',
        'contacts' => 'require',
        'telephone' => 'require',
        'demand_number' => 'require|number',

    ];

    protected $message = [
        'title' =>  '标题不能为空',
        'content'  =>  '内容不能为空',
        'time' => '时间不能为空',
        'address' => '地址不能为空',
        'contacts' => '联系人不能为空',
        'telephone' => '联系方式不能为空',
        'demand_number' => '招募人数不能为空,且只能填写数字',
    ];
}