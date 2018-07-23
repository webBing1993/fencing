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
            $li['title'] = $data['title'];
            $li['create_time'] = time();
            ShopRecord::create($li);
        }
        $this->assign('data',$data);

        return $this->fetch();
    }

    // 商城订单结算页
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