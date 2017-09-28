<?php
/**
 * Created by PhpStorm.
 * User: laowang <958364865@qq.com>
 * Date: 2017/9/27
 * Time: 14:54
 */

namespace app\admin\validate;
use think\Validate;
class Award extends Validate
{
    protected $rule = [
        'front_cover' => "require",
        'name' => "require",
        'sum' => "require"
    ];
    protected $message = [
        'front_cover' => "图片不能为空",
        'name' => "名称不能为空",
        'sum' =>'数量不能为空',
    ];
}