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
        ['front_cover','require','封面大图不能为空'],
        ['image','require','封面小图不能为空'],
        ['title','require','标题不能为空'],
        ['content','require','内容不能为空'],
        ['publisher','require','发布人不能为空']
    ];
}