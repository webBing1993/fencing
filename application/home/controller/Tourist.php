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
        $resutlt = think_ucenter_encrypt('*^'.time().'/%','ztkj');
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
            return $this ->error('验证过期，请重新游客模式二维码进入');
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
            return $this ->error('验证过期，请重新游客模式二维码进入');
        }
    }

    /**
     * 简单对称加密算法之加密
     * @param String $string 需要加密的字串
     * @param String $skey 加密EKY
     * @author Anyon Zou <zoujingli@qq.com>
     * @date 2013-08-13 19:30
     * @update 2014-10-10 10:10
     * @return String
     */
    function encode($string = '', $skey = 'ztkj') {
        $strArr = str_split(base64_encode($string));
        $strCount = count($strArr);
        foreach (str_split($skey) as $key => $value)
            $key < $strCount && $strArr[$key].=$value;
        return str_replace(array('=', '+', '/'), array('O0O0O', 'o000o', 'oo00o'), join('', $strArr));
    }
    /**
     * 简单对称加密算法之解密
     * @param String $string 需要解密的字串
     * @param String $skey 解密KEY
     * @author Anyon Zou <zoujingli@qq.com>
     * @date 2013-08-13 19:30
     * @update 2014-10-10 10:10
     * @return String
     */
    function decode($string = '', $skey = 'ztkj') {
        $strArr = str_split(str_replace(array('O0O0O', 'o000o', 'oo00o'), array('=', '+', '/'), $string), 2);
        $strCount = count($strArr);
        foreach (str_split($skey) as $key => $value)
            $key <= $strCount  && isset($strArr[$key]) && $strArr[$key][1] === $value && $strArr[$key] = $strArr[$key][0];
        return base64_decode(join('', $strArr));
    }
}