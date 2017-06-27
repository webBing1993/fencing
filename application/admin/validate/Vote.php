<?php
/**
 * Created by PhpStorm.
 * User: 老王
 * Date: 2017/1/13
 * Time: 13:11
 */
namespace app\admin\validate;
use think\Validate;

class Vote extends Validate{

    protected $rule = [
        'title' => 'require|max:150',
        'awards' => 'require',
        'front_cover' => 'require',
        'content' => 'require',
        'publisher' => 'require',
        'images' => 'require'
    ];
    protected $message = [
        'title.require' => '题目不能为空',
        'title.max' => '题目长度不能超过50个字',
        'awards.require' => '奖项不能为空',
        'front_cover.require' => '封面图片不能为空',
        'publisher.require' => '发布人不能为空',
        'content.require' => '选项内容不能为空',
        'images.require' => '选项图片不能为空'
    ];
    protected $scene = [
        'act' => ['title','awards','front_cover','publisher'],
        'other' => ['content','images']
    ];
}