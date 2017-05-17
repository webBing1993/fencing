<?php
/**
 * Created by PhpStorm.
 * User: stiff <1570004138@163.com>
 * Date: 2017/4/21
 * Time: 15:12
 */
namespace app\home\controller;
use \think\Request;

class Tourist extends Base{
    /**
     * 游客模式二维码
     */
    public function erweima(){
        //取得加密
        $resutlt = get_md5_token(date('Y/m/d',time()));
        $this ->assign('md5',$resutlt);
        $url = Request::instance()->domain();
        $this ->assign('url',$url);
        return $this ->fetch();
    }
    /**
     * 游客模式登录页
     */
    public function tourist(){
        $result = input('get.str');
        $record = check_md5_token($result);
        $url = Request::instance()->domain();
        $this ->assign('url',$url);
        $this ->assign('md5',$result);
        if($record){
            return $this ->fetch();
        }else{
            return $this ->error('请扫描当日的游客模式二维码进入');
        }

    }
    /**
     * 游客模式仿企业号首页
     */
    public function index(){
        $result = input('get.str');
        $record = check_md5_token($result);
        if($record){
            return $this ->fetch('Index/index');
        }else{
            return $this ->error('请扫描当日的游客模式二维码进入');
        }
    }
    /**
     * 小镇动态
     */
    public function touristdetails1(){
        return $this ->fetch();
    }
    /**
     * 两学一做
     */
    public function touristdetails2(){
        return $this ->fetch();
    }
}