<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/19
 * Time: 14:16
 */

namespace app\home\controller;

use app\home\model\Shop;
use app\home\model\MallOne;
use app\home\model\MallTwo;
use app\home\model\ShopRecord;
use app\home\model\ShopOrder;

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

        return $this->fetch();
    }

    //订单生成(未付款)
    public function ordering(){
        $id = input('id');
        $num = input('num');
        $shop = Shop::where('id',$id)->where('status',0)->find();
        if(empty($shop)){
            return $this->error("该商品已下架");
        }elseif($num < 1){
            return $this->error("请核对下单商品的数量");
        }else{
            $data['sid'] = $id;
            $data['num'] = $num;
            $data['price'] = $shop['price'];
            $data['create_time'] = time();
            $data['create_user'] = session('userId');
            $data['total'] = $num * $shop['price'];
            $ShopOrderModel = new ShopOrder();
            $info = $ShopOrderModel->save($data);
            if($info) {
                $oid = $ShopOrderModel->id;
                return $this->success("订单生成成功",'',$oid);
            }else{
                return $this->error("订单生成失败");
            }
        }
    }

    // 商城订单结算页()
    public function order(){

        return $this->fetch();
    }

    // 商城订单列表
    public function orderList(){

        return $this->fetch();
    }

    // 商城订单详情页
    public function orderDetail(){

        return $this->fetch();
    }
}