<?php
namespace app\home\controller;

use app\home\model\CompetitionApply;
use app\home\model\CompetitionEvent;
use app\home\model\PayRecord;
use app\home\model\WechatUser;
use think\Response;
use wechat\TPWechat;
use wechat\Weixinpay;
use wxpay\JsApiPay;
use wxpay\database\WxPayUnifiedOrder;
use wxpay\WxPayApi;
use wxpay\WxPayConfig;
use Payment\Client\Charge;
use Payment\Common\PayException;
use Payment\Config;

class Pay extends Base
{
    /**
     * 微信支付
     */
    public function wxpay()
    {
        $pid = input('pid');
        $type = input('type');
        $price = input('price');
//        $price = 0.01;
        if (empty($pid) || empty($type) || empty($price)) {
            $this->error('参数错误！');
        }

        $weObj = new TPWechat(config('weixinpay'));
        $uid = session('userId');
        $data = [
            'userid' => $uid,
        ];
        $user = $weObj->convertToOpenId($data);
        $tools = new JsApiPay();

        //统一下单
        $outTradeNo = WxPayConfig::$MCHID . date("YmdHis");
        $input = new WxPayUnifiedOrder();
        $input->setBody("购买支付");
        $input->setAttach("test");
        $input->setOutTradeNo($outTradeNo);
        $input->setTotalFee($price * 100); // 微信支付以分为单位
        $input->setTimeStart(date("YmdHis"));
        $input->setTimeExpire(date("YmdHis", time() + 600));
        $input->setGoodsTag("Reward");
        $input->setNotifyUrl("http://jjg.0519ztnet.com/home/wechat/notify");
        $input->setTradeType("JSAPI");
        $input->setOpenid($user['openid']);
        $order = WxPayApi::unifiedOrder($input);
        $jsApiParameters = $tools->getJsApiParameters($order);

        //生成预支付订单
        $this->handle($pid, $type, $price, $outTradeNo, $uid, 2);

        return $jsApiParameters;
    }


    // 生成alipay订单
    public function alipay() {
        $pid = input('pid');
        $type = input('type');
        $price = input('price');
//        $price = 0.01;
        if (empty($pid) || empty($type) || empty($price)) {
            $this->error('参数错误！');
        }
        $uid = session('userId');
        $aliConfig = config('alipay');

        //统一下单
        $outTradeNo = $aliConfig['app_id'].time() . rand(1000, 9999);
        $payData = [
            'body'    => '购买支付',
            'subject'    => '购买支付',
            'order_no'    => $outTradeNo,
            'timeout_express' => time() + 600,// 表示必须 600s 内付款
            'amount'    => $price ,// 单位为元 ,最小为0.01
            'return_param' => 'tata',// 一定不要传入汉字，只能是 字母 数字组合
            // 'client_ip' => isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1',// 客户地址
            'goods_type' => '1',
            'store_id' => '',
        ];

        try {
            $str = Charge::run(Config::ALI_CHANNEL_WAP, $aliConfig, $payData);
            //生成预支付订单
            $this->handle($pid, $type, $price, $outTradeNo, $uid, 1);

        } catch (PayException $e) {
            return json_encode(['success' => false,'data' => $e->errorMessage()]);
        }

        return json_encode(['success' => true, 'data' => $str]);
    }

    //生成预支付订单
    public function handle($pid, $type, $price, $outTradeNo, $uid, $pay_type) {
        //生成预支付订单
        $model = wechatUser::where(['userid' => $uid])->find();

        if($type == 2){
            $res = competitionApply::where(['id' => $pid])->find();
            $second = competitionApply::where(['competition_id' => $res['competition_id'], 'userid' => $res['userid'], 'group_id' => $res['group_id'], 'remark' => $res['remark'], 'type' => ['<>', $res['type']], 'kinds' => $res['kinds'], 'status' => 0])->find();
            if($second){
                $event_id = competitionApply::where(['id' => $pid])->value('event_id');
                if($event_id){
                    $original_price = competitionEvent::where(['id' => $event_id])->value('price');
                }else{
                    $original_price = $res['price'];
                }
                $info = [
                    'out_trade_no' => $outTradeNo, //唯一订单号
                    'userid' => $uid,
                    'type' => $type,
                    'table' => PayRecord::TYPE_ARRAY[$type],
                    'pid' => $pid,
                    'name' => $model['name'],
                    'price' => $res['price'],
                    'original_price' => $original_price,//原价
                    'pay_type' => $pay_type,//支付类型 1支付宝 2微信 3银联
                    'create_time' => time(),
                ];
                $rs = PayRecord::where(['type' => $type, 'pid' => $pid, 'userid' => $uid, 'status' => 0])->find();
                if(!$rs){
                    PayRecord::create($info);
                }
                $event_id2 = competitionApply::where(['id' => $second['id']])->value('event_id');
                if($event_id2){
                    $original_price2 = competitionEvent::where(['id' => $event_id2])->value('price');
                }else{
                    $original_price2 = $second['price'];
                }
                $info2 = [
                    'out_trade_no' => $outTradeNo, //唯一订单号
                    'userid' => $uid,
                    'type' => $type,
                    'table' => PayRecord::TYPE_ARRAY[$type],
                    'pid' => $second['id'],
                    'name' => $model['name'],
                    'price' => $second['price'],
                    'original_price' => $original_price2,//原价
                    'pay_type' => $pay_type,//支付类型 1支付宝 2微信 3银联
                    'create_time' => time(),
                ];
                $rs2 = PayRecord::where(['type' => $type, 'pid' => $second['id'], 'userid' => $uid, 'status' => 0])->find();
                if(!$rs2){
                    PayRecord::create($info2);
                }
            }else{
                $event_id = competitionApply::where(['id' => $pid])->value('event_id');
                if($event_id){
                    $original_price = competitionEvent::where(['id' => $event_id])->value('price');
                }else{
                    $original_price = $price;
                }
                $info = [
                    'out_trade_no' => $outTradeNo, //唯一订单号
                    'userid' => $uid,
                    'type' => $type,
                    'table' => PayRecord::TYPE_ARRAY[$type],
                    'pid' => $pid,
                    'name' => $model['name'],
                    'price' => $price,
                    'original_price' => $original_price,//原价
                    'pay_type' => $pay_type,//支付类型 1支付宝 2微信 3银联
                    'create_time' => time(),
                ];
                $rs = PayRecord::where(['type' => $type, 'pid' => $pid, 'userid' => $uid, 'status' => 0])->find();
                if(!$rs){
                    PayRecord::create($info);
                }
            }
        }else{
            $original_price = $price;
            $info = [
                'out_trade_no' => $outTradeNo, //唯一订单号
                'userid' => $uid,
                'type' => $type,
                'table' => PayRecord::TYPE_ARRAY[$type],
                'pid' => $pid,
                'name' => $model['name'],
                'price' => $price,
                'original_price' => $original_price,//原价
                'pay_type' => $pay_type,//支付类型 1支付宝 2微信 3银联
                'create_time' => time(),
            ];
            $rs = PayRecord::where(['type' => $type, 'pid' => $pid, 'userid' => $uid, 'status' => 0])->find();
            if(!$rs){
                PayRecord::create($info);
            }
        }
    }

    // 返回支付状态
    public function payStatus() {
        $pid = input('pid');
        $type = input('type');
        if (empty($pid) || empty($type)) {
            $this->error('参数错误！');
        }
        $uid = session('userId');
        $rs = PayRecord::where(['type' => $type, 'pid' => $pid, 'userid' => $uid, 'status' => 1])->find();
        if($rs){
            return $this->success("支付成功");
        }else{
            return $this->error("支付失败");
        }
    }

}