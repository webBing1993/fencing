<?php
/**
 * Created by PhpStorm.
 * User: Lxx<779219930@qq.com>
 * Date: 2017/1/4
 * Time: 10:15
 */

namespace app\admin\controller;
use app\admin\model\Picture;
use app\admin\model\Push;
use app\admin\model\Redfilm;
use app\admin\model\Redbook;
use app\admin\model\Redmusic;
use app\admin\model\Redpush;
use app\admin\model\Redremark;
use com\wechat\TPQYWechat;
use think\Config;

/**
 * Class Redcollection
 * @package app\admin\controller
 * 红色珍藏
 */
class Redcollection extends Admin {
    /**
     * 红色电影
     */
    public function film() {
        $map = array('status' => 1);
        $list = $this->lists("Redfilm",$map);
        int_to_string($list,array(
            'status' => array(0=>"未审核",1=>"已发布"),
        ));
        $this->assign('list',$list);
        
        return $this->fetch();
    } 
    
    /**
     * 电影新增
     */
    public function filmadd() {
        if(IS_POST) {
            $data = input('post.');
            unset($data['id']);
            if($data['recommend'] == 0) {
                unset($data['carousel_image']);
            }
            $data['create_user'] = $_SESSION['think']['user_auth']['id'];
            $filmModel = new Redfilm();
            $info = $filmModel->validate('Redfilm')->save($data);
            if($info) {
                $map = array(
                    'type' => 1,
                    'rid' => $info,
                );
                Redpush::create($map);
                return $this->success('添加成功',Url('Redcollection/film'));
            }else{
                return $this->error($filmModel->getError());
            }
        }else {
            $this->default_pic();
            $this->assign('msg','');
            return $this->fetch('filmedit');
        }
    }
    
    /**
     * 电影修改
     */
    public function filmedit() {
        if(IS_POST) {
            $data = input('post.');
            if($data['recommend'] == 0) {
                $data['carousel_image'] = null;
            }
            $data['update_time'] = time();
            $data['update_user'] = $_SESSION['think']['user_auth']['id'];
            $filmModel = new Redfilm();
            $info = $filmModel->validate('Redfilm')->save($data,['id'=>$data['id']]);
            if($info) {
                return $this->success('修改成功',Url('Redcollection/film'));
            }else{
                return $this->error($filmModel->getError());
            }
        }else {
            $this->default_pic();
            $filmModel = new Redfilm();
            $id = input('id');
            $msg = $filmModel->get($id);
            $this->assign('msg',$msg);
            return $this->fetch();
        }
    }
    
    /**
     * 电影删除
     */
    public function filmdel() {
        $id = input('id');
        $filmModel = new Redfilm();
        $map = array(
            'status' => -1,
        );
        $info = $filmModel->where('id',$id)->update($map);
        if($info) {
            $con = array(
                'type' => 1,
                'rid' => $id
            );
            Redpush::where($con)->update($map);
            return $this->success("删除成功");
        }else {
            return $this->error("删除失败");
        }
    }

    /**
     * 红色音乐
     */
    public function music() {
        $map = array(
            'status' => 1,
        );
        $list = $this->lists('Redmusic',$map);
        int_to_string($list,array(
            'status' => array(0=>"未审核",1=>"已发布"),
        ));
        $this->assign('list',$list);
        
        return $this->fetch();
    }
    
    /**
     * 音乐添加
     */
    public function musicadd() {
        if(IS_POST) {
            $data = input('post.');
            unset($data['id']);
            $data['create_user'] = $_SESSION['think']['user_auth']['id'];
            $musicModel = new Redmusic();
            $info = $musicModel->validate('Redmusic')->save($data);
            if($info) {
                $map = array(
                    'type' => 2,
                    'rid' => $info,
                );
                Redpush::create($map);
                return $this->success("添加成功",Url('Redcollection/music'));
            }else{
                return $this->error($musicModel->getError());
            }
        }else{
            $this->default_pic();
            $this->assign('msg','');
            return $this->fetch('musicedit');
        }
        
    }
    
    /**
     * 音乐修改
     */
    public function musicedit() {
        if(IS_POST) {
            $data = input('post.');
            $data['update_time'] = time();
            $data['update_user'] = $_SESSION['think']['user_auth']['id'];
            $musicModel = new Redmusic();
            $info = $musicModel->validate('Redmusic')->save($data,['id'=>$data['id']]);
            if($info) {
                return $this->success("修改成功",Url('Redcollection/music'));
            }else{
                return $this->error($musicModel->getError());
            }
        }else{
            $this->default_pic();
            $musicModel = new Redmusic();
            $id = input('id');
            $msg = $musicModel->get($id);
            $this->assign('msg',$msg);
            return $this->fetch();
        }
    }
    
    /**
     * 音乐删除
     */
    public function musicdel() {
        $id = input('id');
        $musicModel = new Redmusic();
        $map = array(
            'status' => -1,
        );
        $info = $musicModel->where('id',$id)->update($map);
        if($info) {
            $con = array(
                'type' => 2,
                'rid' => $id
            );
            Redpush::where($con)->update($map);
            return $this->success("删除成功");
        }else {
            return $this->error("删除失败");
        }
    }

    /**
     * 红色文学-书籍
     */
    public function book() {
        $map = array(
            'status' => 1,
        );
        $list = $this->lists('Redbook',$map);
        int_to_string($list,array(
            'status' => array(0=>"未审核",1=>"已发布"),
        ));
        $this->assign('list',$list);
        return $this->fetch();
    }

    /**
     * 文学新增
     */
    public function bookadd() {
        if(IS_POST) {
            $data = input('post.');
            unset($data['id']);
            $data['create_user'] = $_SESSION['think']['user_auth']['id'];
            $bookModel = new Redbook();
            $info = $bookModel->validate('Redbook')->save($data);
            if($info) {
                $map = array(
                    'type' => 3,
                    'rid' => $info,
                );
                Redpush::create($map);
                return $this->success("新增成功",Url('Redcollection/book'));
            }else{
                return $this->error($bookModel->getError());
            }
        }else {
            $this->default_pic();
            $this->assign('msg', '');
            return $this->fetch('bookedit');
        }
    }

    /**
     * 文学修改
     */
    public function bookedit() {
        if(IS_POST) {
            $data = input('post.');
            $data['update_time'] = time();
            $data['update_user'] = $_SESSION['think']['user_auth']['id'];
            $bookModel = new Redbook();
            $info = $bookModel->validate('Redbook')->save($data,['id'=>$data['id']]);
            if($info) {
                return $this->success("修改成功",Url('Redcollection/book'));
            }else{
                return $this->error($bookModel->getError());
            }
        }else{
            $this->default_pic();
            $id = input('id');
            $bookModel = new Redbook();
            $msg = $bookModel->get($id);
            $this->assign('msg',$msg);
            return $this->fetch();
        }
    }

    /**
     * 文学删除
     */
    public function bookdel() {
        $id = input('id');
        $bookModel = new Redbook();
        $map = array(
            'status' => -1,
        );
        $info = $bookModel->where('id',$id)->update($map);
        if($info) {
            $con = array(
                'type' => 3,
                'rid' => $id
            );
            Redpush::where($con)->update($map);
            return $this->success("删除成功");
        }else {
            return $this->error("删除失败");
        }
    }

    /**
     * 经典语录
     */
    public function remark() {
        $map = array(
            'status' => 1,
        );
        $list = $this->lists('Redremark',$map);
        int_to_string($list,array(
            'status' => array(0=>"未审核",1=>"已发布"),
        ));
        $this->assign('list',$list);

        return $this->fetch();
    }

    /**
     * 语录新增
     */
    public function remarkadd() {
        if(IS_POST) {
            $data = input('post.');
            unset($data['id']);
            $data['create_user'] = $_SESSION['think']['user_auth']['id'];
            $data['content'] = json_encode($data['content']);
            $remarkModel = new Redremark();
            $info = $remarkModel->validate('Redremark')->save($data);
            if($info) {
                $map = array(
                    'type' => 4,
                    'rid' => $info,
                );
                Redpush::create($map);
                return $this->success("新增成功",Url('Redcollection/remark'));
            }else{
                return $this->error($remarkModel->getError());
            }
        }else {
            $this->assign('msg', '');
            return $this->fetch('remarkedit');
        }
    }

    /**
     * 语录修改
     */
    public function remarkedit() {
        if(IS_POST) {
            $data = input('post.');
            $data['update_time'] = time();
            $data['update_user'] = $_SESSION['think']['user_auth']['id'];
            $data['content'] = json_encode($data['content']);
            $remarkModel = new Redremark();
            $info = $remarkModel->validate('Redremark')->save($data,['id'=>$data['id']]);
            if($info) {
                return $this->success("修改成功",Url('Redcollection/remark'));
            }else{
                return $this->error($remarkModel->getError());
            }
        }else {
            $id = input('id');
            $remarkModel = new Redremark();
            $msg = $remarkModel->get($id);
            $msg['content'] = json_decode($msg['content'],true);
            $this->assign('msg',$msg);
            return $this->fetch('remarkedit');
        }
    }

    /**
     * 语录删除
     */
    public function remarkdel() {
        $id = input('id');
        $remarkModel = new Redremark();
        $map = array(
            'status' => -1,
        );
        $info = $remarkModel->where('id',$id)->update($map);
        if($info) {
            $con = array(
                'type' => 4,
                'rid' => $id
            );
            Redpush::where($con)->update($map);
            return $this->success("删除成功");
        }else {
            return $this->error("删除失败");
        }
    }

    /**
     * 推送列表
     */
    public function pushlist() {
        if(IS_POST){
            $id = input('id');
            //副图文本周内的新闻消息
            date_default_timezone_set("PRC");        //初始化时区
            $y = date("Y");        //获取当天的年份
            $m = date("m");        //获取当天的月份
            $d = date("d");        //获取当天的号数
            $todayTime= mktime(0,0,0,$m,$d,$y);        //将今天开始的年月日时分秒，转换成unix时间戳
            $time = date("N",$todayTime);        //获取星期数进行判断，当前时间做对比取本周一和上周一时间。
            //$t为本周周一，$s为上周周一
            switch($time){
                case 1: $t = $todayTime;
                    break;
                case 2: $t = $todayTime - 86400*1;
                    break;
                case 3: $t = $todayTime - 86400*2;
                    break;
                case 4: $t = $todayTime - 86400*3;
                    break;
                case 5: $t = $todayTime - 86400*4;
                    break;
                case 6: $t = $todayTime - 86400*5;
                    break;
                case 7: $t = $todayTime - 86400*6;
                    break;
                default:
                    break;
            }
            $info = array(
                'id' => array('neq',$id),
                'create_time' => array('egt',$t),
                'status' => 0,
            );
            $infoes = Redpush::where($info)->select();
            int_to_string($infoes, array(
                'type' => array(1 => "红色电影", 2 => "红色音乐", 3 => "红色书籍", 4 => "红色语录"),
            ));
            return $this->success($infoes);
        }else {
            //消息列表
            $map = array(
                'class' => 4,
                'status' => array('egt', -1),
            );
            $list = $this->lists('Push', $map);
            int_to_string($list, array(
                'status' => array(-1 => '不通过', 0 => '未审核',1 => '已发送'),
            ));
            //数据重组
            foreach ($list as $value) {
                $msg = Redpush::where('id', $value['focus_main'])->find();
                $value['title'] = $msg['title'];
            }
            $this->assign('list', $list);

            //主图文本周内的新闻消息
            date_default_timezone_set("PRC");        //初始化时区
            $y = date("Y");        //获取当天的年份
            $m = date("m");        //获取当天的月份
            $d = date("d");        //获取当天的号数
            $todayTime = mktime(0, 0, 0, $m, $d, $y);  //将今天开始的年月日时分秒，转换成unix时间戳
            $time = date("N", $todayTime);        //获取星期数进行判断，当前时间做对比取本周一和上周一时间。
            //$t为本周周一，$s为上周周一
            switch ($time) {
                case 1:
                    $t = $todayTime;
                    break;
                case 2:
                    $t = $todayTime - 86400 * 1;
                    break;
                case 3:
                    $t = $todayTime - 86400 * 2;
                    break;
                case 4:
                    $t = $todayTime - 86400 * 3;
                    break;
                case 5:
                    $t = $todayTime - 86400 * 4;
                    break;
                case 6:
                    $t = $todayTime - 86400 * 5;
                    break;
                case 7:
                    $t = $todayTime - 86400 * 6;
                    break;
                default:
                    break;
            }
            $info = array(
                'create_time' => array('egt', $t),
                'status' => 0,
            );
            $infoes = Redpush::where($info)->select();
            int_to_string($infoes, array(
                'type' => array(1 => "红色电影", 2 => "红色音乐", 3 => "红色书籍", 4 => "红色语录"),
            ));
            $this->assign('info', $infoes);

            return $this->fetch();
        }
    }

    /**
     * 推送
     */
    public function push() {
        $data = input('post.');
        $arr1 = $data['focus_main'];    //主图文id
        isset($data['focus_vice']) ? $arr2 = $data['focus_vice'] : $arr2 = "";    //副图文id
        if($arr1 == -1){
            return $this->error("请选择主图文!");
        }else {
            //主图文信息
            $focus1 = Redpush::where('id', $arr1)->find();
            switch ($focus1['type']) {
                case 1:
                    $a1 = Redfilm::where('id',$focus1['rid'])->find();
                    $url1 = "http://xspb.0571ztnet.com/home/redcollection/filmdetail/id/".$a1['id'].".html";
                    $pre1 = "【红色电影】";
                    $str1 = strip_tags($a1['introduction']);
                    break;
                case 2:
                    $a1 = Redmusic::where('id',$focus1['rid'])->find();
                    $url1 = "http://xspb.0571ztnet.com/home/redcollection/musicdetail/id/".$a1['id'].".html";
                    $pre1 = "【红色音乐】";
                    $str1 = strip_tags($a1['content']);
                    break;
                case 3:
                    $a1 = Redbook::where('id',$focus1['rid'])->find();
                    $url1 = "http://xspb.0571ztnet.com/home/redcollection/bookdetail/id/".$a1['id'].".html";
                    $pre1 = "【红色书籍】";
                    $str1 = strip_tags($a1['works_introduction']);
                    break;
                case 4:
                    $a1 = Redremark::where('id',$focus1['rid'])->find();
                    $url1 = "http://xspb.0571ztnet.com/home/redcollection/quotaiondetail/id/".$a1['id'].".html";
                    $pre1 = "【经典语录】";
                    $str1 = strip_tags($a1['content']);
                    break;
                default:
                    break;
            }
            $title1 = $a1['title'];
            $des1 = mb_substr($str1,0,100);
            $content1 = str_replace("&nbsp;","",$des1);  //空格符替换成空
            if(isset($a1['front_cover'])) {  //存在封面则读取
                $img1 = Picture::get($a1['front_cover']);
                $path1 = "http://xspb.0571ztnet.com".$img1['path'];
            }else{  //否则获取随机封面
                $b1 = array('1'=>'a','2'=>'b','3'=>'c','4'=>'d','5'=>'e','6'=>'f','7'=>'g','8'=>'h','9'=>'i','10'=>'j','11'=>'k','12'=>'l','13'=>'m','14'=>'n','15'=>'o');
                $front_pic1 = array_rand($b1,1);
                $img1 = Picture::get($front_pic1);
                $path1 = "http://xspb.0571ztnet.com".$img1['path'];
            }
            $information1 = array(
                "title" => $pre1.$title1,
                "description" => $content1,
                "url" => $url1,
                "picurl" => $path1,
            );
        }

        $information = array();
        if(!empty($arr2)) {
            //副图文信息
            $information2 = array();
            foreach ($arr2 as $key=>$value){
                $focus = Redpush::where('id',$value)->find();
                switch ($focus['type']) {
                    case 1:
                        $a = Redfilm::where('id',$focus['rid'])->find();
                        $url = "http://xspb.0571ztnet.com/home/redcollection/filmdetail/id/".$a['id'].".html";
                        $pre = "【红色电影】";
                        $str = strip_tags($a['introduction']);
                        break;
                    case 2:
                        $a = Redmusic::where('id',$focus['rid'])->find();
                        $url = "http://xspb.0571ztnet.com/home/redcollection/musicdetail/id/".$a['id'].".html";
                        $pre = "【红色音乐】";
                        $str = strip_tags($a['content']);
                        break;
                    case 3:
                        $a = Redbook::where('id',$focus['rid'])->find();
                        $url = "http://xspb.0571ztnet.com/home/redcollection/bookdetail/id/".$a['id'].".html";
                        $pre = "【红色书籍】";
                        $str = strip_tags($a['works_introduction']);
                        break;
                    case 4:
                        $a = Redremark::where('id',$focus['rid'])->find();
                        $url = "http://xspb.0571ztnet.com/home/redcollection/quotaiondetail/id/".$a['id'].".html";
                        $pre = "【经典语录】";
                        $str = strip_tags($a['content']);
                        break;
                    default:
                        break;
                }
                $title = $a['title'];
                $des = mb_substr($str,0,100);
                $content = str_replace("&nbsp;","",$des);  //空格符替换成空
                if(isset($a['front_cover'])) {  //存在封面则读取
                    $img = Picture::get($a['front_cover']);
                    $path = "http://xspb.0571ztnet.com".$img['path'];
                }else{  //否则获取随机封面
                    $b = array('1'=>'a','2'=>'b','3'=>'c','4'=>'d','5'=>'e','6'=>'f','7'=>'g','8'=>'h','9'=>'i','10'=>'j','11'=>'k','12'=>'l','13'=>'m','14'=>'n','15'=>'o');
                    $front_pic = array_rand($b,1);
                    $img = Picture::get($front_pic);
                    $path = "http://xspb.0571ztnet.com".$img['path'];
                }
                $info = array(
                    "title" => $pre.$title,
                    "description" => $content,
                    "url" => $url,
                    "picurl" => $path,
                );
                $information2[] = $info;
            }
            //数组合并，主图文放在首位
            foreach ($information2 as $k=>$v){
                $information[0] = $information1;
                $information[$k+1] = $v;
            }
        }else{
            $information[0] = $information1;
        }

        //重组成article数据
        $send = array();
        $re[] = $information;
        foreach ($re as $key => $value){
            $key = "articles";
            $send[$key] = $value;
        }

        //发送给服务号
        $Wechat = new TPQYWechat(Config::get('party'));
        $message = array(
//            'totag' => "18", //审核标签用户
            "touser" => "18768112486",
//            "touser" => "@all",   //发送给全体，@all
            "msgtype" => 'news',
            "agentid" => 21,
            "news" => $send,
            "safe" => "0"
        );
        $msg = $Wechat->sendMessage($message);

        if ($msg['errcode'] == 0){
            $data['focus_vice'] ? $data['focus_vice'] = json_encode($data['focus_vice']) : $data['focus_vice'] = null;
            $data['create_user'] = session('user_auth.username');
            $data['class'] = 4;
            //保存到推送列表
            $s = Push::create($data);
            if($s) {
                return $this->success("发送成功");
            } else{
                return $this->error("发送失败");
            }
        }else{
            return $this->error("发送失败");
        }
    }
}