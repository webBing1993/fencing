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
        'male_time'  =>  'require',
        'female_time'  =>  'require',
        'address'  =>  'require',
        'lng'  =>  'require',
        'lat'  =>  'require',
        'publisher'  =>  'require',
    ];

    protected $message = [
        'front_cover.require'  =>  '请添加封面图片！',
        'title.require' =>  '请添加比赛名称！',
        'end_time.require'  =>  '请选择报名截止时间！',
        'male_time.require'  =>  '请选择男子比赛时间！',
        'female_time.require'  =>  '请选择女子比赛时间！',
        'address.require'  =>  '请填写比赛地址！',
        'lng.require'  =>  '请填写比赛地址经度！',
        'lat.require'  =>  '请填写比赛地址纬度！',
        'publisher.require'  =>  '请填写发布者！',
    ];

}