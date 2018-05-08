<?php
/**
 * Created by PhpStorm.
 * User: laowang
 * Date: 2018/5/8
 * Time: 上午10:56
 */
namespace app\push\controller;

use think\worker\Server;

class Worker extends Server
{
    protected $socket = 'websocket://shop.com:2346';

    /**
     * 收到信息
     * @param $connection
     * @param $data
     */
    public function onMessage($connection, $data)
    {
        $connection->send($data);
    }

    /**
     * 当连接建立时触发的回调函数
     * @param $connection
     */
    public function onConnect($connection)
    {
        $connection->send('我链接上了');
    }

    /**
     * 当连接断开时触发的回调函数
     * @param $connection
     */
    public function onClose($connection)
    {
        $connection->send('我断开了');
    }

    /**
     * 当客户端的连接上发生错误时触发
     * @param $connection
     * @param $code
     * @param $msg
     */
    public function onError($connection, $code, $msg)
    {
        echo "error $code $msg\n";
    }

    /**
     * 每个进程启动
     * @param $worker
     */
    public function onWorkerStart($worker)
    {

    }
}