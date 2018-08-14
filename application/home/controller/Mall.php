<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/19
 * Time: 14:16
 */

namespace app\home\controller;

use app\home\model\PayRecord;
use app\home\model\Shop;
use app\home\model\MallOne;
use app\home\model\MallTwo;
use app\home\model\ShopRecord;
use app\home\model\ShopOrder;
use app\home\model\Picture;
use app\home\model\Venue;
use app\home\model\WechatDepartment;
use app\home\model\WechatUser;
use app\home\model\WechatUserTag;

/*
 * 商城
 * */
class Mall  extends Base
{
    // 商城首页
    public function index(){
        //商品
        $list = Shop::where('status',0)->order('id desc')->limit(12)->select();
        $this->assign('list',$list);
        //剑种
        $type1 = MallOne::where('status',0)->order('id desc')->select();
        $this->assign('type1',$type1);
        //类别
        $type2 = MallTwo::where('status',0)->order('id desc')->select();
        $this->assign('type2',$type2);
        //历史搜索记录
        $ss = ShopRecord::where('status',0)->where('create_user',session('userId'))->order('id desc')->limit(4)->select();
        $this->assign('ss',$ss);

        return $this->fetch();
    }

    // 搜索
    public function search(){
        $val = input('val');
        $data = Shop::where('title','like','%'.$val.'%')->where('status',0)->order('id desc')->select();
        if($data) {
            return $this->success("搜索成功",'',$data);
        }else{
            return $this->error("搜索失败");
        }
    }

    // 筛选
    public function screen(){
        $type1 = input('type1');
        $type2 = input('type2');
        if(!empty($type1)){
            $map['type1'] = $type1;
        }
        if(!empty($type2)){
            $map['type2'] = $type2;
        }
        $map['status'] = 0;
        $data = Shop::where($map)->order('id desc')->limit(12)->select();
        if($data) {
            return $this->success("筛选成功",'',$data);
        }else{
            return $this->error("筛选失败");
        }
    }

    // 上拉加载
    public function more(){
        $type1 = input('type1');
        $type2 = input('type2');
        $len = input('len');
        if(!empty($type1)){
            $map['type1'] = $type1;
        }
        if(!empty($type2)){
            $map['type2'] = $type2;
        }
        $map['status'] = 0;
        $data = Shop::where($map)->order('id desc')->limit($len,8)->select();
        foreach($data as $value){
            $img = Picture::get($value['front_cover']);
            $value['front_cover'] = $img['path'];
        }
        if($data) {
            return $this->success("加载成功",'',$data);
        }else{
            return $this->error("加载失败");
        }
    }

    // 商城详情页
    public function detail(){
        $id = input('id');
        if(empty($id)){
            return $this->error('没有找到此数据');
        }
        $type = input('type');
        if(empty($type)){
            $data = Shop::where('id',$id)->find();
        }else{
            $data = Shop::where('id',$id)->find();
            $li['ssid'] = $data['id'];
            $li['title'] = $data['title'];
            $li['create_user'] = session('userId');
            $li['create_time'] = time();
            ShopRecord::create($li);
        }
        //剑种赋值
        $data['type1'] = MallOne::where('id',$data['type1'])->value('title');
        //类别赋值
        $data['type2'] = MallTwo::where('id',$data['type2'])->value('title');
        $this->assign('data',$data);
        $userId = session('userId');
        $user = WechatUser::where('mobile',$userId)->find();
        $venue_id = WechatUserTag::getVenueId($userId);

        if($venue_id != false AND $user['tag'] == 1 AND $user['vip'] == 1){
            $an = 1;
        }else{
            $an = 0;
        }
        $this->assign('an',$an);

        return $this->fetch();
    }

    //订单生成(未付款)
    public function ordering(){
        $id = input('id');//商品id
        $num = input('num');//数量
        $shop = Shop::where('id',$id)->where('status',0)->find();
        if(empty($shop)){
            return $this->error("该商品已下架");
        }elseif($num < 1){
            return $this->error("请核对下单商品的数量");
        }else{
            $userId = session('userId');
            $count = ShopOrder::where('status',0)->where('sid',$id)->where('num',$num)->where('mobile',$userId)->count();
            if($count == 0){
                $data['sid'] = $id;
                $data['num'] = $num;
//            $data['price'] = $shop['price'];
                $data['create_time'] = time();
                $data['userid'] = session('userId');
//            $data['total'] = $num * $shop['price'];
                $user = WechatUser::where('userid',session('userId'))->find();
                $data['name'] = $user['name'];
                $data['mobile'] = $user['mobile'];
                $venue_id = WechatUserTag::getVenueId($userId);
                $data['depart'] = $venue_id;
                $ShopOrderModel = new ShopOrder();
                $info = $ShopOrderModel->save($data);
                if($info) {
                    $oid = $ShopOrderModel->id;
                    return $this->success("订单生成成功",'',$oid);
                }else{
                    return $this->error("订单生成失败");
                }
            }else{
                $oid = ShopOrder::where('status',0)->where('sid',$id)->where('num',$num)->where('mobile',$userId)->value('id');
                if($oid) {
                    return $this->success("订单生成成功",'',$oid);
                }else{
                    return $this->error("订单生成失败");
                }
            }

        }
    }

    // 商城订单结算页()
    public function order(){
        $id = input('id');
        $data = ShopOrder::where('id',$id)->find();
        $sp = Shop::where('id',$data['sid'])->where('status',0)->find();
        if(empty($sp)){
            return $this->error("该商品已下架");
        }else{
            $data['title'] = $sp['title'];
            $data['price'] = $sp['price'];
            $data['total'] = $sp['price'] * $data['num'];
            $data['front_cover'] = $sp['front_cover'];
            $data['depart'] = Venue::where('id',$data['depart'])->value('title');
        }
        $this->assign('data',$data);

        return $this->fetch();
    }

    // 付钱,更新订单状态
    public function pay(){
        $data = input('post.');
        $order = ShopOrder::where('id',$data['id'])->find();
        $shop = Shop::where('id',$order['sid'])->where('status',0)->find();
        if(empty($shop)){
            return $this->error('此商品已下架');
        }else{
            $data['single'] = $shop['price'];
            $data['all'] = $shop['price'] * $order['num'];
            $data['status'] = 1;
            ShopOrder::update($data);
        }
    }

    // 商城订单列表
    public function orderList(){
        $uid = session('userId');
        $data = ShopOrder::where('userid',$uid)->where('status',1)->order('id desc')->limit(10)->select();
        foreach($data as $k=>$v){
            $li = Shop::where('id',$v['sid'])->find();
            $data[$k]['front_cover'] = $li['front_cover'];
            $data[$k]['title'] = $li['title'];
        }
        $this->assign('data',$data);

        return $this->fetch();
    }

    // 商城订单详情页
    public function orderDetail(){
        $id = input('id');
        $data = ShopOrder::where('id',$id)->find();
        $li = Shop::where('id',$data['sid'])->find();
        $data['front_cover'] = $li['front_cover'];
        $data['title'] = $li['title'];
        $uid = session('userId');
        $zf = PayRecord::where('userid',$uid)->where('type',4)->where('pid',$id)->where('status',1)->find();
        $data['choose'] = $zf['pay_type'];
        $this->assign('data',$data);

        return $this->fetch();
    }

    // 订单页上拉加载
    public function ordermore(){
        $len = input('len');
        $map['status'] = 1;
        $map['create_user'] = session('userId');
        $data = ShopOrder::where($map)->order('id desc')->limit($len,6)->select();
        foreach($data as $value){
            $shop = Shop::where('id',$value['sid'])->find();
            $value['create_time'] = date("Y-m-d H:i",$value['create_time']);
            $img = Picture::get($shop['front_cover']);
            $value['front_cover'] = $img['path'];
            $value['title'] = $shop['title'];
        }

        if($data) {
            return $this->success("加载成功",'',$data);
        }else{
            return $this->error("加载失败");
        }
    }

    // 订单结果页面
    public function orderResult() {
        return $this->fetch();
    }

}