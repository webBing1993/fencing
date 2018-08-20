<?php
/**
 * Created by PhpStorm.
 * User: 王泽锋
 * Date: 2018/07/17
 * Time: 09:33
 */

namespace app\admin\validate;

use think\Validate;

class Competition extends Validate {
    protected $rule = [
        'front_cover'  =>  'require',
        'title' =>  'require',
        'end_time'  =>  'require',
        'address'  =>  'require',
        'lng' =>  'require',
        'lat' =>  'require',
        'content'  =>  'require',
        'publisher'  =>  'require',
    ];

    protected $message = [
        'front_cover.require'  =>  '请添加封面图片！',
        'title.require' =>  '请添加比赛名称！',
        'end_time.require'  =>  '请选择报名截止时间！',
        'address.require'  =>  '请填写地址！',
        'lng.require' =>  '请添加经度！',
        'lat.require' =>  '请添加纬度！',
        'content.require'  =>  '请填写比赛内容！',
        'publisher.require'  =>  '请填写发布者！',
    ];

}