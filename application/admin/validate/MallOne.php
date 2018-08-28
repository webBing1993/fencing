<?php
/**
 * Created by PhpStorm.
 * User: 王泽锋
 * Date: 2018/07/23
 * Time: 15:07
 */

namespace app\admin\validate;

use think\Validate;

class MallOne extends Validate {
    protected $rule = [
        'title' => 'require|max:30',
    ];

    protected $message = [
        'title.require' => '标题不能为空',
        'title.max' => '标题长度不能超过10个字',
    ];

}