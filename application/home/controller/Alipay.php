<?php
/**
 * Created by PhpStorm.
 * User: aion
 * Date: 2017/8/24
 * Time: 上午3:54
 */

namespace app\home\controller;

use app\home\model\PayRecord;
use Payment\Config;
use Payment\Notify\PayNotifyInterface;
use think\Db;
use think\Log;

class Alipay implements PayNotifyInterface
{
    public function notifyProcess(array $data)
    {
        Log::write('---------------------------------支付宝支付回调2---------------------------------');
        Log::record($data);
        $channel = $data['channel'];
        $map = [
            'status' => 0,
            'out_trade_no' => $data['out_trade_no'],
        ];
        $res = PayRecord::where($map)->find();
        if ($res) {
            $res->status = 1;
            $res->save();

            //更新原表支付状态
            Db::name($res['table'])->update(['status' => 1, 'id' => $res['pid']]);
        }

        // 执行业务逻辑，成功后返回true
        return true;
    }
}