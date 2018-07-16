<?php
/**
 * Created by PhpStorm.
 * User: 王泽锋
 * Date: 2018/07/16
 * Time: 14:09
 */

namespace app\admin\validate;

use think\Validate;

class Venue extends Validate {
    protected $rule = [
        'front_cover'  =>  'require',
        'title' =>  'require',
        'address' =>  'require',
        'tel' =>  'require',
        'time' =>  'require',
        'content'  =>  'require',
//        'publisher'  =>  'require',
    ];

    protected $message = [
        'front_cover.require'  =>  '请添加封面图片！',
        'title.require' =>  '请添加场馆名称！',
        'address.require' =>  '请添加详细地址！',
        'tel.require' =>  '请添加联系电话！',
        'time.require' =>  '请添加开放时间！',
        'content.require'  =>  '请填写内容！',
//        'publisher.require'  =>  '请填写发布人名称！',
    ];

}