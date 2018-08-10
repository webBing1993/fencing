<?php
/**
 * Created by PhpStorm.
 * User: 王泽锋
 * Date: 2018/07/20
 * Time: 15:21
 */
namespace app\admin\controller;

use app\admin\model\Picture;
use app\admin\model\WechatUser;
use think\Controller;
use app\admin\model\Comp;

/**
 * Class complaint
 * @package  教练投诉   控制器
 */
class Complaint extends Admin {
    /**
     * 主页列表
     */
    public function index(){
        $map = array(
            'status' => array('egt',0),
        );
        $search = input('search');
        if ($search != '') {
            $map['name'] = ['like', '%' . $search . '%'];
        }
        $list = $this->lists('Comp',$map);

        foreach($list as $k=>$v){
            $list[$k]['create_user'] = WechatUser::where('mobile',$v['create_user'])->value('name');
        }

        //循环遍历
        foreach($list as $key=>$value){
            if(!empty($value['front_cover'])){
                $da = json_decode($value['front_cover']);
                $arr2=array();
                foreach($da as $va){
                    $img = Picture::get($va);
                    $arr2[] = $img['path'];
                }
                $list[$key]['front_cover'] = $arr2;
            }
        }
        int_to_string($list,array(
            'status' => array(0 =>"已发布"),
        ));
        $this->assign('list',$list);

        return $this->fetch();
    }

    /**
     * 教练投诉预览
     */
    public function preview(){
        $Model = new Comp();
        $id = input('id');
        $list = $Model::get($id);
        $list['front_cover'] = json_decode($list['front_cover']);
        $this->assign('list',$list);

        return $this->fetch();
    }


}