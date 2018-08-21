<?php
/**
 * Created by PhpStorm.
 * User: 王泽锋
 * Date: 2018/07/17
 * Time: 09:33
 */

namespace app\admin\validate;

use think\Validate;

class CourseUser extends Validate {
    protected $rule = [
        'member_type'  =>  'require',
        'userid' =>  'require',
    ];

    protected $message = [
        'member_type.require'  =>  '请选择成员类型！',
        'userid.require' =>  '请选择成员！',
    ];

}