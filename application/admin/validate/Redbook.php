<?php
/**
 * Created by PhpStorm.
 * User: Lxx<779219930@qq.com>
 * Date: 2017/1/11
 * Time: 10:57
 */

namespace app\admin\validate;


use think\Validate;

class Redbook extends Validate {
    protected $rule = [
        'title'  =>  'require',
        'front_cover' => 'require',
        'press' =>  'require',
        'publication_date' => 'require',
        'works_introduction'  =>  'require',
        'name'  =>  'require',
        'header'  =>  'require',
        'birth_and_death'  =>  'require',
        'author_introduction'  =>  'require',
    ];

    protected $message = [
        'title.require'  =>  '请填写书籍名称！',
        'front_cover.require' => '请添加书籍封面',
        'press.require' =>  '请填写出版社！',
        'publication_date.require' => '请填写出版时间！',
        'works_introduction.require'  =>  '请填写书籍简介！',
        'name.require'  =>  '请填写作者名称！',
        'header.require'  =>  '请添加作者头像！',
        'birth_and_death.require'  =>  '请填写作者生卒！',
        'author_introduction.require'  =>  '请填写作者简介！',
    ];
}