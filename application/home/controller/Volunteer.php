<?php
/**
 * Created by PhpStorm.
 * User: 老王
 * Date: 2016/11/2
 * Time: 13:21
 */
namespace app\home\controller;
use app\admin\model\WechatDepartment;
use app\home\model\Picture;
use app\home\model\Wish;
use app\home\model\WishReceive;
use app\home\model\WechatUser;

class Volunteer extends Base{
    /**
     * 心愿认领订单
     */
    public function order(){
        $orderModel = new Wish();
        $map = array(
            'status' => array('egt',0)
        );
        $list = $orderModel->where($map)->order('create_time desc')->limit(7)->select();
        $this->assign('list',$list);

        return $this->fetch();
    }

    /**
     * 心愿认领订单订单更多
     */
    public function ordermore() {
        $len = input('length');
        $map =  array(
            'status' => array('egt',0)
        );
        $orderModel = new Wish();
        $list = $orderModel->where($map)->order('create_time desc')->limit($len,5)->select();
        foreach ($list as $value) {
            $img = Picture::get($value['list_image']);
            $value['path'] = $img['path'];
            $value['time'] = date('Y-m-d',$value['create_time']);
        }
        if($list) {
            return $this->success("加载成功","",$list);
        }else{
            return $this->error("加载失败");
        }
    }

    /**
     * 心愿认领订单详情页
     */
    public function orderdetail(){
        $this->anonymous(); //判断是否是游客
        $this->jssdk();

        $userId = session('userId');
        $id = input('id');
        $orderModel = new Wish();
        $info = $orderModel->get($id);
        //分享图片及链接及描述
        $image = Picture::where('id',$info['list_image'])->find();
        $info['share_image'] = "http://".$_SERVER['SERVER_NAME'].$image['path'];
        $info['link'] = "http://".$_SERVER['SERVER_NAME'].$_SERVER['REDIRECT_URL'];
        $info['desc'] = str_replace('&nbsp;','',strip_tags($info['content']));
        //获取用户是否报名
        $map = array(
            'rid' => $id,
            'userid' => $userId,
            'status' => 1,
        );
        $user = WishReceive::where($map)->find();
        if($user) {
            $info['be'] = 1;    //已报名
        }else{
            $info['be'] = 0;    //未报名
        }
        $this->assign('info',$info);

        //名单
        $map1 = array(
            'rid' => $id,
            'status' => array('eq',1),
        );
        $namelist = WishReceive::where($map1)->select();
        foreach ($namelist as $value) {
            $msg = WechatUser::where('userid',$value['userid'])->find();
            $value['avatar'] = $msg['avatar'];
            $value['name'] = $msg['name'];
            foreach(json_decode($msg['department']) as $val){
                $Obj = WechatDepartment::where(['id' =>$val])->find();
                $value['unit'] = $Obj['name'];
            }
        }
        $this->assign('namelist',$namelist);

        return $this->fetch();
    }


    /**
     * 我要报名
     */
    public function enroll() {
        $userId = session('userId');
        $id = input('id');
        $user = WechatUser::where('userid',$userId)->find();
        if($userId == 'visitor') {
            return $this->error("游客无法参与报名！");
        }elseif($user) {
            $model1 = new Wish();
            $model2 = new WishReceive();
            $map = array(
                'rid' => $id,
                'userid' => $userId,
            );
            $father = $model1->get($id);
            //如何人数满则不能报名，改变状态为2
            if($father['status'] == 2) {
                return $this->error("人数已满");
            }else {
                $son = $model2->where($map)->find();
                if($son) {
                    return $this->error("已领取过该订单");
                }else {
                    $info = $model2->create($map);
                    if($info) { //报名成功，领取人数+1；
                        $model1->where('id',$id)->setInc("receive_number");
                        $father = $model1->get($id);
                        if($father['receive_number'] == $father['demand_number']) {  //如果人数已满  改变状态为2
                            $msg['status'] = 2;
                            $model1->where('id',$id)->update($msg);
                        }
                        //返回用户信息
                        $rec = $model2->where($map)->find();
                        $data = WechatUser::where('userid',$rec['userid'])->field('avatar,name,department')->find();
                        $data['time'] = date("Y-m-d",$rec['create_time']);
                        foreach(json_decode($data['department']) as $val){
                            $Obj = WechatDepartment::where(['id' =>$val])->find();
                            $data['department'] = $Obj['name'];
                        }
                        return $this->success("领取成功","",$data);
                    }else {
                        return $this->error("领取失败");
                    }
                }
            }
        }else{
            return $this->error("用户不存在通讯录，无法报名！");
        }
    }

}
