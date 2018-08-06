<?php
/**
 * Created by PhpStorm.
 * User: 王泽锋
 * Date: 2018/07/17
 * Time: 09:33
 */

namespace app\admin\validate;

use think\Validate;

class CompetitionGroup extends Validate {
    protected $rule = [
        'group_name'  =>  'require',
        'start_time' =>  'require',
        'end_time'  =>  'require',
    ];

    protected $message = [
        'group_name.require'  =>  '请添加组别名称！',
        'start_time.require' =>  '请选择开始时间！',
        'end_time.require' =>  '请选择结束时间！',
    ];

}