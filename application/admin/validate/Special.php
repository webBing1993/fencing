<?php
/**
 * Created by PhpStorm.
 * User: 老王
 * Date: 2017/5/10
 * Time: 10:37
 */
namespace app\admin\validate;
use think\Validate;
class Special extends Validate{
    protected $rule = [
        'front_cover' => 'require',
        'title' => 'require',
        'publisher' => 'require',
        'content' => 'require',
    ];
    protected $message = [
        'title.require' => '标题不能为空',
        'content.require' => '内容不能为空',
        'publisher.require' => '发布人不能为空',
        'front_cover.require' => '封面图片不能为空',
        ];
}