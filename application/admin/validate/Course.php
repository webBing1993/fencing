<?php
/**
 * Created by PhpStorm.
 * User: 王泽锋
 * Date: 2018/07/17
 * Time: 09:33
 */

namespace app\admin\validate;

use think\Validate;

class Course extends Validate {
    protected $rule = [
        'type'  =>  'require',
        'course_name' =>  'require',
        'start_time'  =>  'require',
        'end_time'  =>  'require',
        'num'  =>  'require|number',
        'course_time'  =>  'require',
//        'remark'  =>  'require',
    ];

    protected $message = [
        'type.require'  =>  '请选择课程类型！',
        'course_name.require' =>  '请添加课程名称！',
        'start_time.require'  =>  '请选择课程开始时间！',
        'end_time.require'  =>  '请选择课程结束时间！',
        'num.require'  =>  '请填写总课时数！',
        'num.number'  =>  '总课时数必须是数字！',
        'course_time.require'  =>  '请填写上课时间！',
//        'remark.require'  =>  '请填写备注！',
    ];

}