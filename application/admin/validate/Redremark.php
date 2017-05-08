<?php
/**
 * Created by PhpStorm.
 * User: Lxx<779219930@qq.com>
 * Date: 2017/1/12
 * Time: 11:11
 */

namespace app\admin\validate;


use think\Validate;

class Redremark extends Validate {
    protected $rule = [
        'title' =>  'require',
        'content'  =>  'require',
    ];

    protected $message = [
        'title.require' =>  '请填写标题！',
        'content.require'  =>  '请填写内容！',
    ];
}