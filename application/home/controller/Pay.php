<?php
namespace app\home\controller;

use think\Response;
use wechat\TPWechat;
use wechat\Weixinpay;
use wxpay\JsApiPay;
use wxpay\database\WxPayUnifiedOrder;
use wxpay\WxPayApi;
use wxpay\WxPayConfig;

class Pay extends Base
{
    public function index(){
//        $price = input('price');
        $price = 0.01;

        $config = config('weixinpay');
        $weObj = new TPWechat($config);
        $uid = session('userId');
        $data = [
            'userid' => $uid,
        ];
        $user = $weObj->convertToOpenId($data);
        $tools = new JsApiPay();


        $outTradeNo = WxPayConfig::$MCHID . date("YmdHis");
        $input = new WxPayUnifiedOrder();
        $input->setBody("积分购买");
        $input->setAttach("test");
        $input->setOutTradeNo($outTradeNo);
        $input->setTotalFee(1); //积分是一角 微信支付以分为单位
        $input->setTimeStart(date("YmdHis"));
        $input->setTimeExpire(date("YmdHis", time() + 600));
        $input->setGoodsTag("Reward");
        $input->setNotifyUrl("http://jjg.0519ztnet.com/home/wechat/notify");
        $input->setTradeType("JSAPI");
        $input->setOpenid($user['openid']);
        $order = WxPayApi::unifiedOrder($input);

        $jsApiParameters = $tools->getJsApiParameters($order);

        return $jsApiParameters;
    }



}