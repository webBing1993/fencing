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
        'used_num'  =>  'require|number',
    ];

    protected $message = [
        'member_type.require'  =>  '请选择成员类型！',
        'userid.require' =>  '请选择成员！',
        'used_num.require'  =>  '请填写已用课时数！',
        'used_num.number'  =>  '已用课时数必须是数字！',
    ];

}