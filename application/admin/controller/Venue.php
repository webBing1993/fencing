<?php
/**
 * Created by PhpStorm.
 * User: 王泽锋
 * Date: 2018/07/16
 * Time: 13:45
 */

namespace app\admin\controller;

use think\Controller;
use app\admin\model\Venue as VenueModel;

/**
 * Class Venue
 * @package 场馆管理
 */
class Venue extends Admin {

    /**
     * 主页列表
     */
    public function index(){
        $map = array(
            'status' => array('egt',0),
        );
        $list = $this->lists('Venue',$map);

        int_to_string($list,array(
            'status' => array(0=>"已发布",1 =>"已发布",2=>"已推送"),
//            'recommend' => array( 1=>"推荐" , 0=>"不推荐")
        ));
        $this->assign('list',$list);

        return $this->fetch();
    }

    /**
     * 新闻添加
     */
    public function add(){
        if(IS_POST) {
            $data = input('post.');
            if(array_key_exists("front_cover",$data)){

            }else{
                return $this->error("请添加场馆图片");
            }

            $data['create_user'] = $_SESSION['think']['user_auth']['id'];
            if(empty($data['id'])) {
                unset($data['id']);
            }
            isset($data["front_cover"]) ? $data["front_cover"] = json_encode($data["front_cover"]) : $data["front_cover"] = "";
            $newModel = new VenueModel();
            $info = $newModel->validate('Venue')->save($data);
            if($info) {
                return $this->success("添加成功",Url('Venue/index'));
            }else{
                return $this->error($newModel->getError());
            }
        }else{
//            $this->default_pic();
            $this->assign('msg','');

            return $this->fetch('edit');
        }
    }

    /**
     * 修改
     */
    public function edit(){
        if(IS_POST) {
            $data = input('post.');
            isset($data["front_cover"]) ? $data["front_cover"] = json_encode($data["front_cover"]) : $data["front_cover"] = "";
            $newModel = new VenueModel();
            $info = $newModel->validate('Venue')->save($data,['id'=>input('id')]);
            if($info){
                return $this->success("修改成功",Url("Venue/index"));
            }else{
                return $this->get_update_error_msg($newModel->getError());
            }
        }else{
//            $this->default_pic();
            $id = input('id');
            $msg = VenueModel::get($id);
            $msg['front_covers'] = json_decode($msg['front_cover']);
            $this->assign('msg',$msg);
            
            return $this->fetch();
        }
    }

    /**
     * 删除功能
     */
    public function del(){
        $id = input('id');
        $data['status'] = '-1';
        $info = VenueModel::where('id',$id)->update($data);
        if($info) {
            return $this->success("删除成功");
        }else{
            return $this->error("删除失败");
        }

    }

}