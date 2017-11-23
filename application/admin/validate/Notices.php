<?php
/**
 * Created by PhpStorm.
 * User: Lxx<779219930@qq.com>
 * Date: 2016/10/9
 * Time: 16:09
 */

namespace app\admin\validate;


use think\Validate;

class Notices extends Validate {
    protected $rule = [
        'front_cover' => 'require',
        'title' => 'require',
       'description' => 'require',
        'start_time' => 'require',
       /* 'end_time' => 'require',*/
        'address' => 'require',
        'attendee' => 'require',
        'publisher' => 'require',

        'content' => 'require',
    ];

    protected $message = [
        'title.require' => '标题不能为空',
        'front_cover.require' => '图片不能为空',
        'description.require' => '简介不能为空',
        'content.require' => '内容不能为空',
        'start_time.require' => '时间不能为空',
       /* 'end_time.require' => '结束时间不能为空',*/
        'address.require' => '地址不能为空',
       'attendee.require' => '参会人不能为空',
        'publisher.require' => '发布人不能为空',

    ];

}