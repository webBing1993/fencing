<?php
/**
 * Created by PhpStorm.
 * User: Lxx<779219930@qq.com>
 * Date: 2016/10/9
 * Time: 16:09
 */

namespace app\admin\validate;


use think\Validate;

class Activity extends Validate {
    protected $rule = [
        'front_cover' => 'require',
        'title' => 'require',
        
        'publisher' => 'require',
        'content' => 'require',
    ];

    protected $message = [
        'title.require' => '标题不能为空',
        'front_cover.require' => '图片不能为空',
       
        'content.require' => '内容不能为空',
        'publisher.require' => '发布人不能为空',
    ];

}