<?php
namespace app\admin\validate;
use think\Validate;

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/7/18
 * Time: 9:46
 */

class Appraise extends Validate
{
    protected $rule = [
        'title' => 'require',
        'front_cover' => 'require',
        'content' => 'require',
        'publisher' => 'require',
        'images' => 'require'
    ];
    protected $message = [
        'title.require' => '题目不能为空',
        'front_cover.require' => '封面图片不能为空',
        'publisher.require' => '发布人不能为空',
        'content.require' => '选项内容不能为空',
        'images.require' => '选项图片不能为空'
    ];
    protected $scene = [
        'act' => ['title','front_cover','publisher'],
        'other' => ['content','images']
    ];
}