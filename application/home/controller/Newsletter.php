<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/19
 * Time: 14:16
 */

namespace app\home\controller;


/*
 * 通讯名录
 * */
use app\home\model\WechatDepartment;

class Newsletter  extends Base
{
    // 通讯名录首页
    public function index(){
        $one = WechatDepartment::where('parentid',1)->select();
        foreach($one as $k=>$v){
            $two = WechatDepartment::where('parentid',$v['id'])->select();
            if(!empty($two)){
                foreach($two as $k2=>$v2){
                    $three = WechatDepartment::where('parentid',$v2['id'])->select();
                    if(!empty($three)){
                        $two[$k2]['three'] = $three;
                        $two[$k2]['threepd'] = 1;
                    }else{
                        $two[$k2]['threepd'] = 0;
                    }
                }
                $one[$k]['twopd'] = 1;
                $one[$k]['two'] = $two;

            }else{
                $one[$k]['twopd'] = 0;
            }
        }
        $this->assign('one',$one);

        return $this->fetch();
    }

    // 通讯名录列表页
    public function userlist(){


        return $this->fetch();
    }

    // 通讯名录详情页
    public function detail(){


        return $this->fetch();
    }


}