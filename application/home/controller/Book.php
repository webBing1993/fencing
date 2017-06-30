<?php
/**
 * Created by PhpStorm.
 * User: zyf
 * QQ: 2205446834@qq.com
 * Date: 2017/6/28
 * Time: 16:26
 */

namespace app\home\controller;
use app\home\model\WechatDepartment;
use app\home\model\WechatUser;
class Book extends Base
{
 /*   通讯录首页*/
    public function  index(){
//       $user=WechatUser::where('userid',session("userId"))->find();
//       $hand=WechatDepartment::where('parentid',0)->find();
//       $department=$this->findChild($hand);
//       $this->assign('department',$department);
//        $this->assign('user',$user);
//       $sd=$this-> mydeparment();
        return $this->fetch();
    }
    //全部部门的方法
    public function  findChild($hand){
        $child=WechatDepartment::where('parentid', $hand['id'])->select();
        if($child) {
            $hand['child'] = $child;
            foreach ($hand['child'] as $child) {
              $child=$this->findChild($child);
            }
        return $hand;
        }
        else{
            $hand['child'] = "";
          return $hand;
        }
    }
 //只看自己部门的方法
    public function  mydeparment(){
       $wd=new  WechatDepartment();
        $user=WechatUser::where('userid',session("userId"))->find();
       $dep=WechatDepartment::where('id',$user['department'])->find();
        $did=array();
       $did=$wd->findallfartherbychild($dep,$did);


echo json_encode($did);
  return $did;
    }
    /*   通讯录用户列表*/
    public function  userlist(){
      //  $id=input('id');
     //   $userlist=WechatUser::where('deparment',$id)->select();
      //  $this->assign("list",$userlist);
        return $this->fetch();
    }
    /*   通讯录用户详情*/
    public function  userinfo(){
        $id=input('userid');
        $userinfo=  WechatUser::where('userid',$id)->select();
        $this->assign("info",$userinfo);
        return $this->fetch();
    }



}