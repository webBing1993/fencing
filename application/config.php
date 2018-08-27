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
        'login' => 'http://jjg.0519ztnet.com/home/Verify/login',
        'token' => '',
        'encodingaeskey' => '',
        'appid' => 'wwf507973ebf84282e',
        'appsecret' => 'mPpwyW2_khw7PVfZ8Ow0EVM93oghffubbR0klvyQUR4',
        'agentid' => 1000002,
    ),

    //个人中心消息通知模块
    'user' => [
        'login' => 'http://jjg.0519ztnet.com/home/Verify/login',
        'appid' => 'wwf507973ebf84282e',
        'appsecret' => '
6qO-TYMIVkF93qB8nsQbJjOxi5AW5L8LZOLT192nTNk',
        'agentid' => 1000003
    ],

    //通讯录模块
    'mail' => array(
        'login' => 'http://jjg.0519ztnet.com/home/Verify/login',
        'appid' => 'wwf507973ebf84282e',
        'appsecret' => 'wb6TMsGAwwY0-xg_NRUsg8YBTa44z2c-yFdlXFNkpgQ',
        'agentid' => 1000002
    ),

    //  推送网站域名
    'http_url' => "http://ben.zt.cn",
    // 推送对象 发送给全体 @all  个人测试15700004138
    'touser' => '18767104335',
    'totestuser' => '18767104335',
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
    /* 支付宝 */
    'alipay' => [
        'use_sandbox' => false,// 是否使用沙盒模式
        'partner'     => '2088921675491734',
        'app_id'      => '2018080960945646',
        'sign_type'   => 'RSA2',// RSA  RSA2

        // 可以填写文件路径，或者密钥字符串  当前字符串是 rsa2 的支付宝公钥(开放平台获取)
//        'ali_public_key'  => 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAihthOCftodBNy329HNe/idSShNPiKviLj1kuBxW/2wA90+cf+RSPIR6MezieM9KTKM8ARWSLrO9cyvV9bgF0mq2KyOeAnUDCmD+ANqYZ137BxayuZIan5ZHg4MhI5m8n3vIVskVKudCa+31bxsmmaEa1X+qK6W247dqP59Y+Pg1pQqzgHEjFSNBQ0q7pMw9kL/y4zU4fCDQa9jIxrNeegoBrotBlOYocdoM+1PWeePLSELMOtNXJrHVYCArzjc4tKs8gjKEKCA/sxmLuJ/rZIJQ9C6+d0wLSqi8LYxR3PGkpGK0YIIEq4eoGz6Xkm2XIDAYB6glWMIh3EcLD+7cFswIDAQAB',
        'ali_public_key' => 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA69JpXWbsi0coC9IXZfUOQ1JQN0r92DBHCN96OSsAFnp7jbvm1vjGVhbeHVRcc+VLynMrjm01cCDO2cCc0Kk9DEdgAzjtI0Q7L3PqF4CjXAC+dIUfYji5ipcEqqLPliU1ryQ/2EIkfEkH2GEsnghW046sf5UCdvBBUsarZ9eagS1Ff+TA6wmCm3iSix6/xJMCpOIIwUwxYureDgEdLhr7efZGiwrptzCRFyZXjuQQ4Zhux9sXyPwH+J8Zx0bBm+0Zk5ZQX0/rpZ2gpt21A1lTKsm+U3Ggty+om1H8mxrmFVcg9Q949CM33/Gd6HSrdzOQr+Mlad4NTXxDthoBakzpSwIDAQAB',
        // 可以填写文件路径，或者密钥字符串  我的沙箱模式，rsa与rsa2的私钥相同，为了方便测试
        'rsa_private_key' => 'MIIEowIBAAKCAQEA69JpXWbsi0coC9IXZfUOQ1JQN0r92DBHCN96OSsAFnp7jbvm1vjGVhbeHVRcc+VLynMrjm01cCDO2cCc0Kk9DEdgAzjtI0Q7L3PqF4CjXAC+dIUfYji5ipcEqqLPliU1ryQ/2EIkfEkH2GEsnghW046sf5UCdvBBUsarZ9eagS1Ff+TA6wmCm3iSix6/xJMCpOIIwUwxYureDgEdLhr7efZGiwrptzCRFyZXjuQQ4Zhux9sXyPwH+J8Zx0bBm+0Zk5ZQX0/rpZ2gpt21A1lTKsm+U3Ggty+om1H8mxrmFVcg9Q949CM33/Gd6HSrdzOQr+Mlad4NTXxDthoBakzpSwIDAQABAoIBAAnZOr9E2iIi8aA0wPdoGZVkLadxgVJzCbRsKN2UVyeZK+nKbCSUNSxJPjN5X3cTn2uwDaSgGPg61oivi50HRF5r6K2ZxgWQBuy/aYpbu/bOrSWOnbXraoxILFC4BfVeTnSnDeeJM57hKeu2ezeKX53sOnYFkhnHhOLhGy12CqTUW6nMD6/p5TWtLLgZE4IHl6TL083/jyX+VH7rDCMe5jnYVp6XRC9RBFoV2NgabSwgPkUhUbeUha8dWLSGmKu/4kVCRxfOe75RWP1pLVL0xTrDQ+6mwd3pjl84PoeYkoiFK9SqlW844nZ95cGrk9THFYsEwyQCqqyX/DwQ0P8QBoECgYEA9s3xQI7L0GE4gclK2X8DNR5jr2+b1LcwWE8PRchjXSkNH+q/P13o69/rnQJ51E7O2uK+T8VJnpQXG/1NJPG/gJiqD53obSi2PxKMsdj7e6SuH2mbgsko0uF7XvfZ3NTSQNsiV5Jh34KTX3XwfnZo8mG+MF2XgNOAF8Sn8IL63IsCgYEA9Ju3UHlGeg3hL5j5EalCM3CoAimfazXVCkJOzt6+4a8LtvRMhzgmeN8aR4Hp+hz9i4kdQ2Ta+vbQZx6AMApfKxDCIVILx74ucAGMJmo9pi6bsjBjrXC/EYO39dq1qOeA0hYlbYkyRN/oFqSGd5v4g1utmBjrqgIzdzuE3EF7/kECgYB0q7RhhV+aGWuCBytIF//nK3+KXqN28kZ7aJxaq7k1QAPBU9Km3PkQEGGsbpAzKjIlTU0UHrqHqZAnHh5K2bFi1EiIa2iFudo1hMpYBLAVqiraFK18LSYMUqVkppwqBb2DBVR9u2hjJbIPTYyr68P+2O/0oznMn68NDV0qmHCIswKBgFBDvH5inTa10TuuJaSe9sMsZ2T2RYKHkpWgNn35Z4dkyiJJvcbtLOqiwtvODHnvA1/DKWAQ+y44yR/dSVD6Rko6YIlQg98ST+ifCV8do4chaiOpbzvdcEUOBNJe5xc6h/HoJHXx/BXPpzeu2xTqkDyCW8859jK+MCci17Dyp/5BAoGBANjojdYmrnBKS8btP4AnBSgSG/Hk/so06DwFduFaPQfgADiadX1WsXGKWKcJBB5KkrIYNyAwymNL5t4IlsxPEaaG0gYZDlWlU2zOGq4mVCHNS7apjEo2B33AcKG85S3TOIxeCdBjvqluMO1on7Bq7yu+YoiguTR7g2U1P/6Zd9YK',

        'limit_pay' => [
            //'balance',// 余额
            //'moneyFund',// 余额宝
            //'debitCardExpress',// 	借记卡快捷
            //'creditCard',//信用卡
            //'creditCardExpress',// 信用卡快捷
            //'creditCardCartoon',//信用卡卡通
            //'credit_group',// 信用支付类型（包含信用卡卡通、信用卡快捷、花呗、花呗分期）
        ],// 用户不可用指定渠道支付当有多个渠道时用“,”分隔

        // 与业务相关参数
        'notify_url' => 'http://jjg.0519ztnet.com/home/Wechat/alipayNotify',
        'return_url' => 'http://jjg.0519ztnet.com/home/Wechat/returnUrl',

        'return_raw' => true,// 在处理回调时，是否直接返回原始数据，默认为 true
    ],
];
