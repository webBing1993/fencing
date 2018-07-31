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
        'title' =>  'require',
        'region' =>  'require',
        'address' =>  'require',
        'lat' =>  'require',
        'lng' =>  'require',
        'tel' =>  'require',
        'time' =>  'require',
        'content'  =>  'require',
        'front_cover'  =>  'require',
//        'publisher'  =>  'require',
    ];

    protected $message = [
        'title.require' =>  '请添加场馆名称！',
        'region.require' =>  '请添加场馆所在区域！',
        'address.require' =>  '请添加详细地址！',
        'lat.require' =>  '请添加纬度！',
        'lng.require' =>  '请添加经度！',
        'tel.require' =>  '请添加联系电话！',
        'time.require' =>  '请添加开放时间！',
        'content.require'  =>  '请填写内容！',
        'front_cover.require'  =>  '请添加场馆图片！',
//        'publisher.require'  =>  '请填写发布人名称！',
    ];

}