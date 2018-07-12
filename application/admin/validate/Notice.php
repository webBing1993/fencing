<?php
/**
 * Created by PhpStorm.
 * User: 王泽锋
 * Date: 2018/07/12
 * Time: 14:34
 */

namespace app\admin\validate;

use think\Validate;

class Notice extends Validate {
    protected $rule = [
        'front_cover' => 'require',
        'title' => 'require|max:150',
        'content' => 'require',
        'publisher' => 'require',
    ];

    protected $message = [
        'title.require' => '标题不能为空',
        'title.max' => '标题长度不能超过50个字',
        'front_cover.require' => '图片不能为空',
        'content.require' => '内容不能为空',
        'publisher.require' => '发布人不能为空',
    ];

}