<?php
/**
 * Created by PhpStorm.
 * User: Lxx<779219930@qq.com>
 * Date: 2017/1/10
 * Time: 14:37
 */

namespace app\admin\validate;


use think\Validate;

class Redmusic extends Validate {
    protected $rule = [
        'front_cover'  =>  'require',
        'title' =>  'require',
        'content'  =>  'require',
    ];

    protected $message = [
        'front_cover.require'  =>  '请添加封面图片！',
        'title.require' =>  '请填写音乐名称！',
        'content.require'  =>  '请填写内容！',
    ];
}