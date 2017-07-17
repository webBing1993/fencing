<?php
namespace app\admin\controller;
use app\admin\model\Special as SpecialModel;
/**
 * Created by PhpStorm.
 * User: 老王
 * Date: 2017/7/17
 * Time: 14:09
 */
/*
  专题模块  控制器
*/
class Special extends Admin
{
    /*
     * 主题  管理
     */
    public function topic(){
        $map = array(
            'type' => 1, // 主题
            'status' => array('egt',0),
        );
        $list = $this->lists('Special',$map);
        int_to_string($list,array(
            'status' => array(0=>"已发布",1=>"已发布"),
        ));

        $this->assign('list',$list);
        return $this->fetch();
    }
    /*
     * 主题  内容 添加
     */
    public function add(){
        if(IS_POST) {
            $data = input('post.');
            $Model = new SpecialModel();
            if(empty($data['id'])) {
                unset($data['id']);
            }
            $data['create_user'] = $_SESSION['think']['user_auth']['id'];
            if ($data['type'] == 1){
                $model = $Model->validate('Special.act')->save($data);
            }else{
                $model = $Model->validate('Special.other')->save($data);
            }
            if($model){
                if ($data['type'] == 1){
                    return $this->success('新增主题成功',Url('Special/topic'));
                }else if($data['type'] == 2){
                    return $this->success('新增内容成功',Url('Special/index'));
                }
            }else{
                return $this->get_update_error_msg($Model->getError());
            }
        }else{
            $msg = array();
            $msg['type'] = input('type');
            $msg['class'] = 1; // 1为添加 ，2为修改
            $this->assign('msg',$msg);
            return $this->fetch('edit');
        }
    }
    /*
     * 主题  内容 添加 修改
     */
    public function edit(){
        if(IS_POST) {
            $data = input('post.');
            $Model = new SpecialModel();
            if ($data['type'] == 1){
                $model = $Model->validate('Special.act')->save($data,['id'=> $data['id']]);
            }else{
                $model = $Model->validate('Special.other')->save($data,['id'=> $data['id']]);
            }
            if($model){
                if ($data['type'] == 1){
                    return $this->success('修改主题成功',Url('Special/topic'));
                }else if($data['type'] == 2){
                    return $this->success('修改内容成功',Url('Special/index'));
                }
            }else{
                return $this->get_update_error_msg($Model->getError());
            }
        }else{
            $this->default_pic();
            $id = input('id');
            $msg = SpecialModel::get($id);
            $msg['class'] = 2;
            $msg['type'] = input('type');
            $this->assign('msg',$msg);

            return $this->fetch();
        }
    }
    /*
     * 内容 列表
     */
    public function index(){
        $map = array(
            'type' => 2, // 内容
            'status' => array('egt',0),
        );
        $list = $this->lists('Special',$map);
        int_to_string($list,array(
            'status' => array(0=>"已发布",1=>"已发布"),
        ));

        $this->assign('list',$list);
        return $this->fetch();
    }
    /*
     * 内容 删除
     */
    public function del(){
        $id = input('id');
        if (empty($id)){
            return $this->error('系统参数错误,请重新选择');
        }
        $res = SpecialModel::where(['id' => $id])->update(['status' => -1]);
        if ($res){
            return $this->success('删除成功');
        }else{
            return $this->error('删除失败');
        }
    }
}