<?php
/**
 * Created by PhpStorm.
 * User: 老王
 * Date: 2017/5/12
 * Time: 9:44
 */
namespace app\admin\validate;
use think\Validate;
class  Company extends Validate{
    protected $rule = [
        'image' => 'require',
        'title' => 'require',
        'content' => 'require',
        'start_time' => 'require',
        'activity_home' => 'require',
        'contacts' => 'require',
        'tel' => 'require',
        'demand_number' => 'require',
        'publisher' => 'require',

    ];

    protected $message = [
        'image.require' => '图片不能为空',
        'title.require' => '标题不能为空',
        'content.require' => '内容不能为空',
        'start_time.require' => '活动时间不能为空',
        'activity_home.require' => '活动地点不能为空',
        'contacts.require' => '联系人不能为空',
        'tel.require' => '联系方式不能为空',
        'demand_number.require' => '招募人数不能为空',
        'publisher.require' => '发布人不能为空',
    ];

}