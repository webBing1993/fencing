<?php
/**
 * Created by PhpStorm.
 * User: 王泽锋
 * Date: 2018/07/23
 * Time: 15:07
 */

namespace app\admin\validate;

use think\Validate;

class Shop extends Validate {
    protected $rule = [
        'type1' => 'require',
        'type2' => 'require',
        'front_cover' => 'require',
        'title' => 'require|max:150',
        'price' => 'require',
        'content' => 'require',
    ];

    protected $message = [
        'type1.require' => '请添加剑种',
        'type2.require' => '请添加类别',
        'title.require' => '标题不能为空',
        'title.max' => '标题长度不能超过50个字',
        'front_cover.require' => '图片不能为空',
        'price.require' => '价格不能为空',
        'content.require' => '内容不能为空',
    ];

}