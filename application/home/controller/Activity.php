<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/10
 * Time: 9:03
 */

namespace app\home\controller;

use app\home\model\Notice;
use think\Db;
class Activity extends Base
{
    /**
     * @return mixed  主页
     */
    public function index(){
        $Notice = new Notice();
        $mapp = ['status' => ['egt',0],'type' => 1];
        $leftone=Db::table('pb_notice')->where($mapp)->order('create_time desc')->limit(2)->select();//活动安排
        $mapp = ['status' => ['egt',0],'type' => 2];//活动展示
        $lefttwo = $Notice->get_list($mapp);
        $mapp = ['status' => ['egt',0],'type' => 3];//会议纪要
        $center = $Notice->get_list($mapp);
        $mapp = ['status' => ['egt',0],'type' => 4];//固定活动
        $right = $Notice->get_list($mapp);
        //dump($right);exit();

        $this->assign('leftone',$leftone); // 活动安排
        $this->assign('lefttwo',$lefttwo); // 活动展示
        $this->assign('center',$center); // 会议纪要
        $this->assign('right',$right);  // 固定活动
        return $this->fetch();
    }

    /*
    *  更多活动
    */
    public  function morelist(){
        $Notice = new Notice();
        $mapp = ['status' => ['egt',0],'type' => 1];//活动展示
        $info = $Notice->get_list($mapp);
        
        $this->assign('info',$info);
        return $this ->fetch();
    }

    /**
     * 详情页
     */
    public function detail2(){
        $this->anonymous();
        $this->jssdk();
        $id = input('id/d');
        $info = $this->content(4,$id);
        //dump($info);
        //exit();
        $this->assign('detail',$info);
        return $this->fetch();

    }

    /**
     * 列表加载更多
     */
    public function more(){
        $Special = new Special();
        $len = input('length');
        $c = input('type');
        if ($c == 0){
            $type = 1;  //政策解读
        }else{
            $type = 2; //通知公告
        }
        $map = ['status' => ['egt',0] , 'type' => $type];
        $list = $Special->get_list($map,$len);
        if ($list){
            return $this->success('加载成功','',$list);
        }else{
            return $this->error('加载失败');
        }
    }

    /*
     *  发布和填写
     */
    public  function publish(){
        return $this ->fetch();
    }



}