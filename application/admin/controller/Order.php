<?php
/**
 * Created by PhpStorm.
 * User: 王泽锋
 * Date: 2018/07/25
 * Time: 14:30
 */
namespace app\admin\controller;

use app\admin\model\Shop;
use app\admin\model\ShopOrder;
use think\Controller;
use app\admin\model\Push;
use app\admin\model\Show as ShowModel;

/**
 * Class Order
 * @package  风采展示   控制器
 */
class Order extends Admin {
    /**
     * 主页列表 教练管理
     */
    public function index(){
        $map = array(
            'status' => array('eq',1),
        );
        $list = $this->lists('ShopOrder',$map);
        foreach($list as $k=>$v){
            $list[$k]['title'] = Shop::where('id',$v['sid'])->value('title');
        }
        int_to_string($list,array(
            'status' => array(1 =>"已付款"),
        ));
        $this->assign('list',$list);

        return $this->fetch();
    }

    public function preview(){
        $id = input('id');
        $list = ShopOrder::where('id',$id)->find();
        $shop = Shop::where('id',$list['sid'])->find();
        $list['title'] = $shop['title'];
        $list['img'] = $shop['front_cover'];
        $this->assign('list',$list);

        return $this->fetch();
    }

}