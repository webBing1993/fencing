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
        'login' => 'http://tzgxpb.0571ztnet.com/home/verify/login',
//        'token' => 'ZfqkOC4Fhd7D',
//        'encodingaeskey' => 'SWWdiNibALNG3hZfvqqAzQ48rmibi5KTc1JnqouajTC',
        'appid' => 'wwe2d5103c1343a8bb',
        'appsecret' => 'agp2IBdsHpDef0lKv8tdcnDIS0k-iHiR5yoNy_A-J34',
        'agentid' => 1000002
    ),
    

    /* UC用户中心配置 */
    'uc_auth_key' => '(.t!)=JTb_OPCkrD:-i"QEz6KLGq5glnf^[{p;je'
    
];
