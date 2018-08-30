<?php
/**
 * Created by PhpStorm.
 * User: 王泽锋
 * Date: 2018/07/25
 * Time: 14:30
 */
namespace app\admin\controller;

use app\admin\model\Picture;
use app\admin\model\Shop;
use app\admin\model\ShopOrder;
use think\Controller;

/**
 * Class Order
 * @package  订单管理   控制器
 */
class Order extends Admin {
    /**
     * 订单管理
     */
    public function index(){
        $map = array(
            'status' => array('eq',1),
        );
        $search = input('search');
        if ($search != '') {
            $map['venue_name|name|mobile'] = ['like', '%' . $search . '%'];
        }
        $list = $this->lists('ShopOrder',$map);

        foreach($list as $k=>$v){
            $list[$k]['title'] = Shop::where('id',$v['sid'])->value('title');
            $list[$k]['create_time'] = date("Y-m-d H:i",$v['create_time']);
            $a = Shop::where('id',$v['sid'])->value('front_cover');
            $img = Picture::get($a);
            $list[$k]['front_cover'] = $img['path'];
        }
        int_to_string($list,array(
            'status' => array(1 =>"已付款"),
        ));
        $this->assign('list',$list);

        return $this->fetch();
    }

    //预览
    public function preview(){
        $id = input('id');
        $list = ShopOrder::where('id',$id)->find();
        $shop = Shop::where('id',$list['sid'])->find();
        $list['title'] = $shop['title'];
        $list['img'] = $shop['front_cover'];
        $this->assign('list',$list);

        return $this->fetch();
    }

    //确认发货
    public function confirm(){
        $id = input('id');
        $map['confirm'] = 1;
        $info = ShopOrder::where('id',$id)->update($map);
        if($info) {
            return $this->success("确认发货成功");
        }else{
            return $this->error("确认发货失败");
        }
    }

}