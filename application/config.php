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
    'app_debug' => true,

    /* URL配置 */
    'base_url'=>'',
    'parse_str'=>[
        '__ROOT__' => '/',
        '__STATIC__' => '/static',
        '__ADMIN__' => '/admin',
        '__HOME__' => '/home',
    ],
    
    /* 企业配置 */
    'party' => array(
        'token' => '',
        'encodingaeskey' => '',
        'appid' => '',
        'appsecret' => '',
    ),
    /* UC用户中心配置 */
    'uc_auth_key' => '(.t!)=JTb_OPCkrD:-i"QEz6KLGq5glnf^[{p;je',

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
