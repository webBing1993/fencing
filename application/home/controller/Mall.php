<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/19
 * Time: 14:16
 */

namespace app\home\controller;

use app\home\model\News;
use app\home\model\Venue;

/*
 * 商城
 * */
class Mall  extends Base
{
    // 商城首页
    public function index(){

        return $this->fetch();
    }

    // 商城详情页
    public function detail(){

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