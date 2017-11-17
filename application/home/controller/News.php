<?php
/**
 * Created by PhpStorm.
 * User: Lxx<779219930@qq.com>
 * Date: 2016/9/12
 * Time: 15:56
 */

namespace app\home\controller;

use app\home\model\News as NewsModel;
/**
 * Class News
 * @package 箬横动态
 */
class News extends Base {
    /**
     * 主页
     */
    public function index(){
        $NewsModel = new NewsModel();
        $map = ['status' => ['eq',0],'type' => 1];
        $left = $NewsModel->get_list($map);
        $mapp = ['status' => ['eq',0],'type' => 2];
        $right = $NewsModel->get_list($mapp);
        $this->assign('left',$left); // 基层建设
        $this->assign('right',$right);  // 党政建设
        return $this->fetch();
    }

    /**
     * 详情页
     */
    public function detail(){
        $this->anonymous();
        $this->jssdk();
        $id = input('id/d');
        $info = $this->content(2,$id);
        //dump($info);exit();
        $this->assign('detail',$info);



        return $this->fetch();
    }

    /**
     * 列表加载更多
     */
    public function listmore(){
        $NewsModel = new NewsModel();
        $len = input('length');
        $c = input('type');
        //dump($c);
        //exit();
        if ($c == 0){
            $type = 1;  //基层动态
        }else{
            $type = 2; //党建动态
        }
        $map = ['status' => ['egt',0] , 'type' => $type];
        $list = $NewsModel->get_list($map,$len);
        if ($list){
            return $this->success('加载成功','',$list);
        }else{
            return $this->error('加载失败');
        }
    }

}