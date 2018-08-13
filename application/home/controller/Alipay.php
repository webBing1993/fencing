<?php
/**
 * Created by PhpStorm.
 * User: aion
 * Date: 2017/8/24
 * Time: 上午3:54
 */

namespace app\home\controller;

use app\common\model\PayLog;
use app\common\model\PointsLog;
use app\common\model\ThirdUser;
use app\index\model\WechatUser;
use Payment\Config;
use Payment\Notify\PayNotifyInterface;
use think\Log;

class Alipay implements PayNotifyInterface
{
    public function notifyProcess(array $data)
    {
        $channel = $data['channel'];
        $map = [
            'status' => 0,
            'order_id' => $data['out_trade_no'],
        ];
        $res = PayLog::where($map)->find();
        if ($res) {
            $res->status = 1;
            $res->save();

            //新增购买记录
            $data = [
                'user_id' => $res['user_id'],
                'user_type' => $res['user_type'],
                'type' => 2, //积分购买
                'amount' => $res['points'],
            ];
            PointsLog::create($data);

            //修改用户积分
            if ($res['user_type'] == 1) {
                WechatUser::where('userid',$res['user_id'])->setInc('points', $res['points']);
            } else if ($res['user_type'] == 2) {
                ThirdUser::where('user_id',$res['user_id'])->setInc('points', $res['points']);
            }
        }

        // 执行业务逻辑，成功后返回true
        return true;
    }
}