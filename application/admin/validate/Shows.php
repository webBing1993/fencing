<?php
/**
 * Created by PhpStorm.
 * User: 王泽锋
 * Date: 2018/07/17
 * Time: 13:59
 */

namespace app\admin\validate;

use think\Validate;

class Shows extends Validate {
    protected $rule = [
        'front_cover'  =>  'require',
        'name' =>  'require',
        'sex' =>  'require',
        'school' =>  'require',
        'class' =>  'require',
        'site' =>  'require',

//        'level' =>  'require',
//        'content'  =>  'require',
//        'publisher'  =>  'require',
    ];

    protected $message = [
        'front_cover.require'  =>  '请添加照片！',
        'name.require' =>  '请添加姓名！',
        'sex.require' =>  '请添加性别！',
        'school.require' =>  '请添加就读学校！',
        'class.require' =>  '请添加剑种！',
        'site.require' =>  '请添加训练场馆！',
//        'level.require' =>  '请添加岗位级别！',
//        'content.require'  =>  '请填写自我介绍！',
//        'publisher.require'  =>  '请填写发布人名称！',
    ];

}