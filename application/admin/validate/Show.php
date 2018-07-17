<?php
/**
 * Created by PhpStorm.
 * User: 王泽锋
 * Date: 2018/07/17
 * Time: 13:59
 */

namespace app\admin\validate;

use think\Validate;

class Show extends Validate {
    protected $rule = [
        'front_cover'  =>  'require',
        'name' =>  'require',
        'sex' =>  'require',
        'class' =>  'require',
        'year' =>  'require',
        'prize' =>  'require',
        'level' =>  'require',
        'content'  =>  'require',
//        'publisher'  =>  'require',
    ];

    protected $message = [
        'front_cover.require'  =>  '请添加照片！',
        'name.require' =>  '请添加姓名！',
        'sex.require' =>  '请添加性别！',
        'class.require' =>  '请添加剑种！',
        'year.require' =>  '请添加执教年限！',
        'prize.require' =>  '请添加所获奖项！',
        'level.require' =>  '请添加岗位级别！',
        'content.require'  =>  '请填写自我介绍！',
//        'publisher.require'  =>  '请填写发布人名称！',
    ];

}