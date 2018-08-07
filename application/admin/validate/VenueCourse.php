<?php
/**
 * Created by PhpStorm.
 * User: 王泽锋
 * Date: 2018/07/17
 * Time: 09:33
 */

namespace app\admin\validate;

use think\Validate;

class VenueCourse extends Validate {
    protected $rule = [
        'front_cover'  =>  'require',
        'type'  =>  'require',
        'course_name' =>  'require',
        'start_time'  =>  'require',
        'end_time'  =>  'require',
        'num'  =>  'require',
        'course_time'  =>  'require',
        'content'  =>  'require',
        'remark'  =>  'require',
        'price'  =>  'require',
    ];

    protected $message = [
        'front_cover.require'  =>  '请添加封面图片！',
        'type.require'  =>  '请选择课程类型！',
        'course_name.require' =>  '请添加课程名称！',
        'start_time.require'  =>  '请选择课程开始时间！',
        'end_time.require'  =>  '请选择课程结束时间！',
        'num.require'  =>  '请填写总课时数！',
        'course_time.require'  =>  '请填写上课时间！',
        'content.require'  =>  '请填写课程内容！',
        'remark.require'  =>  '请填写备注！',
        'price.require'  =>  '请填写价格！',
    ];

}