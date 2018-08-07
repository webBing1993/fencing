<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
// $Id$

return [
    'url_route_on' => true,
//    'log'          => [
//        'type' => 'trace', // 支持 socket trace file
//    ],

    /* 默认模块和控制器 */
    'default_module' => 'home',

    'http_exception_template' => [
        // 定义404错误的重定向页面地址
        404 => APP_PATH . 'admin/view/base/404.html',
        // 还可以定义其它的HTTP status
        401 => APP_PATH . 'admin/view/base/401.html',
        500 => APP_PATH . 'admin/view/base/500.html',
    ],

    /* URL配置 */
    'base_url'=>'',
    'parse_str'=>[
        '__ROOT__' => '/',
        '__STATIC__' => '/static',
        '__ADMIN__' => '/admin',
        '__HOME__' => '/home',
    ],

    /* 企业配置   新手指南*/
    'party' => array(
        'login' => 'http://zzxz.0571ztnet.com/home/index/login',
        'token' => '',
        'encodingaeskey' => '',
        'appid' => 'wwf507973ebf84282e',
        'appsecret' => 'mPpwyW2_khw7PVfZ8Ow0EVM93oghffubbR0klvyQUR4',
        'agentid' => 1000002,
    ),

    //  推送网站域名
    'http_url' => "http://ben.zt.cn",
    // 推送对象 发送给全体 @all  个人测试15700004138
    'touser' => '18767104335',
    /* UC用户中心配置 */
    'uc_auth_key' => '(.t!)=JTb_OPCkrD:-i"QEz6KLGq5glnf^[{p;je',
    // 关闭调试模式
    'app_debug' => true,
    // 显示错误信息
    'show_error_msg'        =>  true,

    /* 微信支付 */
    'weixinpay'       => [
        'appid'       => 'wwf507973ebf84282e', // 微信支付appid
        'mchid'       => '1496568072', // 微信支付mchid 商户收款账号
        'key'         => 'e10adc3949ba59abbe56e057f20f883a', // 微信支付key
        'appsecret'   => 'F16SLtStcOYDVXCeVeuriQ8l-mVjnyZBAAoM7aiYbhU', // 公众帐号secert (公众号支付专用)
        'notify_url' => 'http://jjg.0519ztnet.com/home/wechat/notify', // 接收支付状态的连接
        'agentid' => '3010046',
    ],
];
