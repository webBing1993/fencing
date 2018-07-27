<?php
/**
 * Created by PhpStorm.
 * User: 王泽锋
 * Date: 2018/07/17
 * Time: 13:53
 */
namespace app\admin\controller;

use think\Controller;
use app\admin\model\Picture;
use app\admin\model\Push;
use com\wechat\TPQYWechat;
use app\admin\model\Show as ShowModel;
use think\Config;

/**
 * Class Show
 * @package  风采展示   控制器
 */
class Show extends Admin {
    /**
     * 主页列表 教练管理
     */
    public function index(){
        $map = array(
            'type' => array('eq',1),
            'status' => array('egt',0),
        );
        $search = input('search');
        if ($search != '') {
            $map['name'] = ['like', '%' . $search . '%'];
        }
        $list = $this->lists('Show',$map);
        int_to_string($list,array(
            'status' => array(0 =>"已发布"),
        ));

        $this->assign('list',$list);

        return $this->fetch();
    }

    /**
     * 教练添加
     */
    public function add(){
        if(IS_POST) {
            $data = input('post.');
            $data['create_user'] = $_SESSION['think']['user_auth']['id'];
            if(empty($data['id'])){
                unset($data['id']);
            }
            $newModel = new ShowModel();
            $info = $newModel->validate('Show')->save($data);
            if($info) {
                return $this->success("添加成功",Url('Show/index'));
            }else{
                return $this->error($newModel->getError());
            }
        }else{
            $this->assign('msg','');

            return $this->fetch('edit');
        }
    }

    /**
     * 教练修改
     */
    public function edit(){
        if(IS_POST) {
            $data = input('post.');
//            $data['create_time'] = time();
            $newModel = new ShowModel();
            $info = $newModel->validate('Show')->save($data,['id'=>input('id')]);
            if($info){
                return $this->success("修改成功",Url("Show/index"));
            }else{
                return $this->get_update_error_msg($newModel->getError());
            }
        }else{
            $id = input('id');
            $msg = ShowModel::get($id);
            $this->assign('msg',$msg);

            return $this->fetch();
        }
    }

    /**
     * 教练删除功能
     */
    public function del(){
        $id = input('id');
        $data['status'] = '-1';
        $info = ShowModel::where('id',$id)->update($data);
        if($info) {
            return $this->success("删除成功");
        }else{
            return $this->error("删除失败");
        }
    }


    /**
     * 批量删除
     */
    public function moveToTrash()
    {
        $ids = input('ids/a');
        if (!$ids) {
            return $this->error('请勾选删除选项');
        }
        $data['status'] = '-1';
        $info = ShowModel::where('id', 'in', $ids)->update($data);

        if ($info) {
            return $this->success('批量删除成功', url('index'));
        } else {
            return $this->error('批量删除失败');
        }
    }

    /**
     * 主页列表 教练管理
     */
    public function indexs(){
        $map = array(
            'type' => array('eq',2),
            'status' => array('egt',0),
        );
        $search = input('search');
        if ($search != '') {
            $map['name'] = ['like', '%' . $search . '%'];
        }
        $list = $this->lists('Show',$map);
        int_to_string($list,array(
            'status' => array(0 =>"已发布"),
        ));

        $this->assign('list',$list);

        return $this->fetch();
    }

    /**
     * 教练添加
     */
    public function adds(){
        if(IS_POST) {
            $data = input('post.');
            $data['create_user'] = $_SESSION['think']['user_auth']['id'];
            if(empty($data['id'])){
                unset($data['id']);
            }
            $newModel = new ShowModel();
            $info = $newModel->validate('Shows')->save($data);
            if($info) {
                return $this->success("添加成功",Url('Show/indexs'));
            }else{
                return $this->error($newModel->getError());
            }
        }else{
            $this->assign('msg','');

            return $this->fetch('edits');
        }
    }

    /**
     * 教练修改
     */
    public function edits(){
        if(IS_POST) {
            $data = input('post.');
//            $data['create_time'] = time();
            $newModel = new ShowModel();
            $info = $newModel->validate('Shows')->save($data,['id'=>input('id')]);
            if($info){
                return $this->success("修改成功",Url("Show/indexs"));
            }else{
                return $this->get_update_error_msg($newModel->getError());
            }
        }else{
            $id = input('id');
            $msg = ShowModel::get($id);
            $this->assign('msg',$msg);

            return $this->fetch();
        }
    }

    /**
     * 教练删除功能
     */
    public function dels(){
        $id = input('id');
        $data['status'] = '-1';
        $info = ShowModel::where('id',$id)->update($data);
        if($info) {
            return $this->success("删除成功");
        }else{
            return $this->error("删除失败");
        }
    }

    /**
     * 批量删除
     */
    public function moveToTrashs()
    {
        $ids = input('ids/a');
        if (!$ids) {
            return $this->error('请勾选删除选项');
        }
        $data['status'] = '-1';
        $info = ShowModel::where('id', 'in', $ids)->update($data);

        if ($info) {
            return $this->success('批量删除成功', url('indexs'));
        } else {
            return $this->error('批量删除失败');
        }
    }
}