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
        ['title','require','标题不能为空'],
        ['content','require','内容不能为空'],
        ['publisher','require','发布人不能为空']
    ];
}