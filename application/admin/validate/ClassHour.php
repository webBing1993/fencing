<?php
/**
 * Created by PhpStorm.
 * User: 王泽锋
 * Date: 2018/07/17
 * Time: 09:33
 */

namespace app\admin\validate;

use think\Validate;

class ClassHour extends Validate {
    protected $rule = [
        'class_name' =>  'require',
        'start_time'  =>  'require',
        'end_time'  =>  'require',
        'num'  =>  'require|number',
    ];

    protected $message = [
        'class_name.require' =>  '请添加课时名称！',
        'start_time.require'  =>  '请选择课时开始时间！',
        'end_time.require'  =>  '请选择课时结束时间！',
        'num.require'  =>  '请填写课时数！',
        'num.number'  =>  '课时数必须是数字！',
    ];

}