<?php
/**
 * Created by PhpStorm.
 * User: 老王
 * Date: 2017/5/10
 * Time: 10:37
 */
namespace app\admin\validate;
use think\Validate;
class Culture extends Validate{
    protected $rule = [
        'title' => 'require',
        'content' => 'require',
        'publisher' => 'require',
        'front_cover' => 'require',
        'address' => 'require',
        'telephone' => 'require'
    ];
    protected $message = [
        'title.require' => '标题不能为空',
        'content.require' => '内容不能为空',
        'publisher.require' => '发布人不能为空',
        'front_cover.require' => '封面图片不能为空',
        'address.require' =>'地址不能为空',
        'telephone.require' =>'联系方式不能为空'
        ];
    protected $scene = [
        'act' => ['title','content','publisher'],
        'other' => ['title','content','publisher','front_cover'],
        'others' => ['title','address','telephone','content','publisher','front_cover']
    ];
}