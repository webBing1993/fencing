<?php

namespace app\home\controller;


use app\home\model\PayRecord;
use think\Db;
use think\Loader;
use think\Log;
use wechat\TPWechat;
use think\Controller;
use app\index\model\Order;
use wxpay\database\WxPayUnifiedOrder;
use wxpay\JsApiPay;
use wxpay\NativePay;
use wxpay\PayNotifyCallBack;
use wxpay\WxPayApi;
use wxpay\WxPayConfig;
use Payment\Client\Notify;
use Payment\Common\PayException;

class Wechat extends Controller
{
    public function index() {
        phpinfo();



    }

    /**
     * 微信支付返回
     */
    public function notify() {
        Log::write('---------------------------------微信支付回调---------------------------------');
        //接受成功回调支付信息
        $xml = file_get_contents('php://input');
        $arr = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        Log::record($arr);
        $map = [
            'status' => 0,
            'out_trade_no' => $arr['out_trade_no'],
        ];

        //修改订单
        $res = PayRecord::where($map)->find();
        if ($res) {
            $res->status = 1;
            $res->save();

            $res2 = PayRecord::where(['out_trade_no' => $data['out_trade_no']])->select();
            foreach ($res2 as $val) {
                //更新原表支付状态
                Db::name($val['table'])->update(['status' => 1, 'id' => $val['pid']]);
            }

            //更改会员状态
            if ($res['type'] == 3) {
                $rs = Db::name($res['table'])->where(['id' => $res['pid']])->find();
                Db::name('wechat_user')->where(['userid' => $rs['userid']])->update(['vip' => 1, 'viptime' => $rs['time']]);
            }

            //返回成功
            $return = ['return_code' => 'SUCCESS', 'return_msg' => 'OK'];
            $xml = '<xml>';
            foreach ($return as $k => $v) {
                $xml .= '<' . $k . '><![CDATA[' . $v . ']]></' . $k . '>';
            }
            $xml .= '</xml>';

            echo $xml;
        }

    }

    // alipay支付返回
    public function alipayNotify() {
        Log::write('---------------------------------支付宝支付回调---------------------------------');
        if(!empty($_POST)){
            $data = $_POST;
            Log::record($data);
            if (!empty($data['trade_no'])) {
                $map = [
                    'status' => 0,
                    'out_trade_no' => $data['out_trade_no'],
                ];
                $res = PayRecord::where($map)->find();
                if ($res) {
                    $res->status = 1;
                    $res->save();

                    $res2 = PayRecord::where(['out_trade_no' => $data['out_trade_no']])->select();
                    foreach ($res2 as $val) {
                        //更新原表支付状态
                        Db::name($val['table'])->update(['status' => 1, 'id' => $val['pid']]);
                    }

                    //更改会员状态
                    if ($res['type'] == 3) {
                        $rs = Db::name($res['table'])->where(['id' => $res['pid']])->find();
                        Db::name('wechat_user')->where(['userid' => $rs['userid']])->update(['vip' => 1, 'viptime' => $rs['time']]);
                    }
                }
            }
        }
        echo 'success';
        exit;

        /*$aliConfig = config('alipay');

        $callback = new Alipay();
        $type = 'ali_charge';// xx_charge
        try {
            $retData = Notify::getNotifyData($type, $aliConfig);// 获取第三方的原始数据，未进行签名检查
            Log::record($retData);
            $ret = Notify::run($type, $aliConfig, $callback);// 处理回调，内部进行了签名检查
        } catch (PayException $e) {
            return json_encode(['success' => false,'data' => $e->errorMessage()]);
        }

        Log::record($ret);
        echo $ret;
        exit;*/
    }

    public function returnUrl(){
        return $this->fetch('public/returnurl');
    }

    // 兼容服务号错误
    public function toalipay() {
        return $this->fetch('public/toalipay');
    }
}