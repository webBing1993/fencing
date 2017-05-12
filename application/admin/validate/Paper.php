<?php
/**
 * Created by PhpStorm.
 * User: 老王
 * Date: 2017/5/11
 * Time: 10:34
 */
namespace app\admin\validate;
use think\Validate;
class Paper extends Validate{
    protected $rule = [
        'title' => 'require',
        'content' => 'require',
        'publisher' => 'require'
    ];
    protected $message = [
        'title.require' => '标题不能为空',
        'content.require' => '内容不能为空',
        'publisher' => '发布人不能为空'
    ];
}