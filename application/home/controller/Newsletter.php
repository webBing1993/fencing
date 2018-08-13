<?php
/**
 * Created by PhpStorm.
 * User: 王泽锋
 * Date: 2018/08/10
 * Time: 14:16
 */
namespace app\home\controller;

use app\home\model\Venue;
use app\home\model\WechatDepartment;
use app\home\model\WechatDepartmentUser;
use app\home\model\WechatUser;
use app\home\model\WechatUserTag;

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

    /**
     * 首字符排序
     * @param $s 用户姓名
     * @return bool|string
     */
    protected function getFirstChar($s)
    {
        $s0 = mb_substr($s,0,3); // 获取名字的姓
//        $s = iconv('UTF-8','gbk', $s0); // 将UTF-8转换成GB2312编码
        $s = mb_convert_encoding($s0, "gb2312", "utf-8");
        if (ord($s0)>128) { // 汉字开头，汉字没有以U、V开头的
            $asc=ord($s{0})*256+ord($s{1})-65536;
            if($asc>=-20319 and $asc<=-20284)return "A";
            if($asc>=-20283 and $asc<=-19776)return "B";
            if($asc>=-19775 and $asc<=-19219)return "C";
            if($asc>=-19218 and $asc<=-18711)return "D";
            if($asc>=-18710 and $asc<=-18527)return "E";
            if($asc>=-18526 and $asc<=-18240)return "F";
            if($asc>=-18239 and $asc<=-17760)return "G";
            if($asc>=-17759 and $asc<=-17248)return "H";
            if($asc>=-17247 and $asc<=-17418)return "I";
            if($asc>=-17417 and $asc<=-16475)return "J";
            if($asc>=-16474 and $asc<=-16213)return "K";
            if($asc>=-16212 and $asc<=-15641)return "L";
            if($asc>=-15640 and $asc<=-15166)return "M";
            if($asc>=-15165 and $asc<=-14923)return "N";
            if($asc>=-14922 and $asc<=-14915)return "O";
            if($asc>=-14914 and $asc<=-14631)return "P";
            if($asc>=-14630 and $asc<=-14150)return "Q";
            if($asc>=-14149 and $asc<=-14091)return "R";
            if($asc>=-14090 and $asc<=-13319)return "S";
            if($asc>=-13318 and $asc<=-12839)return "T";
            if($asc>=-12838 and $asc<=-12557)return "W";
            if($asc>=-12556 and $asc<=-11848)return "X";
            if($asc>=-11847 and $asc<=-11056)return "Y";
            if($asc>=-11055 and $asc<=-10247)return "Z";
        } else if (ord($s)>=48 and ord($s)<=57) { // 数字开头
            switch(iconv_substr($s,0,1,'utf-8')){
                case 1:return "Y";
                case 2:return "E";
                case 3:return "S";
                case 4:return "S";
                case 5:return "W";
                case 6:return "L";
                case 7:return "Q";
                case 8:return "B";
                case 9:return "J";
                case 0:return "L";
            }
        } else if (ord($s)>=65 and ord($s)<=90) { // 大写英文开头

            return substr($s,0,1);
        } else if (ord($s)>=97 and ord($s)<=122) { // 小写英文开头

            return strtoupper(substr($s,0,1));
        } else { // 中英混合的词语，不适合上面的各种情况，因此直接提取首个字符即可

            return iconv_substr($s0,0,1,'utf-8');
        }
    }

    // 通讯名录列表页
    public function userlist(){
        $id = input('id');
        $this->assign('bmid',$id);
        $user = WechatDepartmentUser::where('departmentid',$id)->select();
        $charArray = [];
        foreach($user as $k=>$v){
            $user[$k]['name'] = WechatUser::where('mobile',$v['userid'])->value('name');
            $img = WechatUser::where('mobile',$v['userid'])->find();
            if(!empty($img['header'])){
                $user[$k]['img'] = $img['header'];
            }else{
                $user[$k]['img'] = $img['avatar'];
            }
            $user[$k]['uid'] = $img['id'];
            $char = $this->getFirstChar($user[$k]['name']);
            $nameArray = [];

            if (isset($charArray[$char]) && count($charArray[$char])!=0) {
                $nameArray = $charArray[$char];
            }
            array_push($nameArray,$v);
            $charArray[$char] = $nameArray;
        }

        ksort($charArray);
//        dump($charArray);exit;
        $this->assign('charArray',$charArray);
        $key = array_keys($charArray);
        $this->assign('key',$key);

        return $this->fetch();
    }

    // 通讯名录详情页
    public function detail(){
        $Id = input('id');
        $user = WechatUser::where('id',$Id)->find();
        $userId = $user['userid'];
        $venue_id = WechatUserTag::getVenueId($userId);
        if($venue_id == false){
            $user['bm'] = '暂无场馆';
        }else{
            $user['bm'] = Venue::where('id',$venue_id)->value('title');
        }
        $this->assign('user',$user);

        return $this->fetch();
    }

    /**
     * 首页搜索
     */
    public function search() {
        $val = input('val');
        if($val) {
            $map = [
                'name' => ['like','%'.$val.'%'],
            ];
            $map2 = [
                'mobile' => ['like','%'.$val.'%'],
            ];
            $list = WechatUser::where($map)->whereOr($map2)->column('id,name,mobile');
            if($list) {
                return $this->success("查询成功","",$list);
            }else{
                return $this->error("未查询到数据");
            }
        }else {
            return $this->error("查询条件不能为空");
        }
    }

    /**
     * 部门内搜索
     */
    public function search2() {
        $val = input('val');
        if($val) {
            $map = [
                'name' => ['like','%'.$val.'%'],
            ];
            $map2 = [
                'mobile' => ['like','%'.$val.'%'],
            ];
            $list = WechatUser::where($map)->whereOr($map2)->column('id,name,mobile,header,avatar');
            if($list) {
                return $this->success("查询成功","",$list);
            }else{
                return $this->error("未查询到数据");
            }
        }else {
            return $this->error("查询条件不能为空");
        }
    }

}