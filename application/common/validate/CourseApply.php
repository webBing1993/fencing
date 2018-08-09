<?php
/**
 * Created by PhpStorm.
 * User: 王泽锋
 * Date: 2018/07/17
 * Time: 09:33
 */

namespace app\admin\validate;

use think\Validate;

class CourseApply extends Validate {
    protected $rule = [
        'venue_id'  =>  'require',
        'venue_name'  =>  'require',
        'course_id'  =>  'require',
        'course_name'  =>  'require',
        'userid' =>  'require',
        'type' =>  'require',
        'num'  =>  'require',
        'start_time'  =>  'require',
        'end_time'  =>  'require',
        'price' =>  'require',
    ];

    protected $message = [
        'venue_id.require'  =>  '参数缺失！',
        'venue_name.require'  =>  '参数缺失！',
        'course_id.require'  =>  '参数缺失！',
        'course_name.require'  =>  '参数缺失！',
        'userid.require' =>  '参数缺失！',
        'type.require' =>  '参数缺失！',
        'num.require' =>  '参数缺失！',
        'start_time.require'  =>  '参数缺失！',
        'end_time.require'  =>  '参数缺失！',
        'price.require' =>  '参数缺失！',
    ];

}