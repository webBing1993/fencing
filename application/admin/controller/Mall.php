<?php
/**
 * Created by PhpStorm.
 * User: 王泽锋
 * Date: 2018/07/23
 * Time: 14:08
 */

namespace app\admin\controller;

use think\Db;
use app\admin\model\MallOne as MallOneModel;
use app\admin\model\MallTwo as MallTwoModel;

/**
 * Class Mall
 * @package   购物商城   控制器
 */
class Mall extends Admin {
    /**
     * 剑种主页列表
     */
    public function index(){
        $map = array(
            'status' => array('eq',0),
        );
        $list = $this->lists('MallOne',$map);
        int_to_string($list,array(
            'status' => array(0 =>"已发布"),
        ));

        $this->assign('list',$list);

        return $this->fetch();
    }

    /**
     * 剑种添加
     */
    public function add(){
        if(IS_POST) {
            $data = input('post.');
            if(empty($data['title'])){
                return $this->error("请添加剑种名称");
            }
//            $data['create_user'] = $_SESSION['think']['user_auth']['id'];
            if(empty($data['id'])){
                unset($data['id']);
            }
            $newModel = new MallOneModel();
            $data['create_time']=time();
            $info = $newModel->save($data);
            if($info) {
                return $this->success("添加成功",Url('Mall/index'));
            }else{
                return $this->error($newModel->getError());
            }
        }else{
            $this->assign('msg','');

            return $this->fetch('edit');
        }
    }

    /**
     * 剑种修改
     */
    public function edit(){
        if(IS_POST) {
            $data = input('post.');
            if(empty($data['title'])){
                return $this->error("请添加剑种名称");
            }
            $newModel = new MallOneModel();
            $info = $newModel->save($data,['id'=>input('id')]);
            if($info){
                return $this->success("修改成功",Url("Mall/index"));
            }else{
                return $this->get_update_error_msg($newModel->getError());
            }
        }else{
            $id = input('id');
            $msg = MallOneModel::get($id);
            $this->assign('msg',$msg);

            return $this->fetch();
        }
    }

    /**
     * 剑种删除功能
     */
    public function del(){
        $id = input('id');
        $data['status'] = '-1';
        $info = MallOneModel::where('id',$id)->update($data);
        if($info) {
            return $this->success("删除成功");
        }else{
            return $this->error("删除失败");
        }
    }

    /**
     * 类别主页列表
     */
    public function tp(){
        $map = array(
            'status' => array('eq',0),
        );
        $list = $this->lists('MallTwo',$map);
        int_to_string($list,array(
            'status' => array(0 =>"已发布"),
        ));

        $this->assign('list',$list);

        return $this->fetch();
    }

    /**
     * 类别添加
     */
    public function add2(){
        if(IS_POST) {
            $data = input('post.');
            if(empty($data['title'])){
                return $this->error("请添加类别名称");
            }
//            $data['create_user'] = $_SESSION['think']['user_auth']['id'];
            if(empty($data['id'])){
                unset($data['id']);
            }
            $newModel = new MallTwoModel();
            $data['create_time']=time();
            $info = $newModel->save($data);
            if($info) {
                return $this->success("添加成功",Url('Mall/tp'));
            }else{
                return $this->error($newModel->getError());
            }
        }else{
            $this->assign('msg','');

            return $this->fetch('edit2');
        }
    }

    /**
     * 类别修改
     */
    public function edit2(){
        if(IS_POST) {
            $data = input('post.');
            if(empty($data['title'])){
                return $this->error("请添加类别名称");
            }
            $newModel = new MallTwoModel();
            $info = $newModel->save($data,['id'=>input('id')]);
            if($info){
                return $this->success("修改成功",Url("Mall/tp"));
            }else{
                return $this->get_update_error_msg($newModel->getError());
            }
        }else{
            $id = input('id');
            $msg = MallTwoModel::get($id);
            $this->assign('msg',$msg);

            return $this->fetch();
        }
    }

    /**
     * 类别删除功能
     */
    public function del2(){
        $id = input('id');
        $data['status'] = '-1';
        $info = MallTwoModel::where('id',$id)->update($data);
        if($info) {
            return $this->success("删除成功");
        }else{
            return $this->error("删除失败");
        }
    }


}