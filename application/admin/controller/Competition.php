<?php
/**
 * Created by PhpStorm.
 * User: 王泽锋
 * Date: 2018/07/17
 * Time: 09:28
 */
namespace app\admin\controller;

use think\Controller;
use app\admin\model\Picture;
use app\admin\model\Push;
use com\wechat\TPQYWechat;
use app\admin\model\Competition as CompetitionModel;
use think\Config;

/**
 * Class Competition
 * @package  比赛报名控制器
 */
class Competition extends Admin {
    /**
     * 主页列表
     */
    public function index(){
        $map = array(
            'status' => array('egt',0),
        );
        $search = input('search');
        if ($search != '') {
            $map['title'] = ['like', '%' . $search . '%'];
        }
        $list = $this->lists('Competition',$map);
        int_to_string($list,array(
            'status' => array(0 =>"已发布",1=>"已推送"),
        ));

        $this->assign('list',$list);

        return $this->fetch();
    }

    /**
     * 添加
     */
    public function add(){
        if(IS_POST) {
            $data = input('post.');
            $data['create_user'] = $_SESSION['think']['user_auth']['id'];
            if(empty($data['id'])){
                unset($data['id']);
            }
            if($data['end_time']){
                $data['end_time'] = strtotime($data['end_time']);
            }
            if($data['male_time']){
                $data['male_time'] = strtotime($data['male_time']);
            }
            if($data['female_time']){
                $data['female_time'] = strtotime($data['female_time']);
            }
            $competitionModel = new CompetitionModel();
            $info = $competitionModel->validate('Competition')->save($data);
            if($info) {
                return $this->success("添加成功",Url('Competition/index'));
            }else{
                return $this->error($competitionModel->getError());
            }
        }else{
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
            if($data['end_time']){
                $data['end_time'] = strtotime($data['end_time']);
            }
            if($data['male_time']){
                $data['male_time'] = strtotime($data['male_time']);
            }
            if($data['female_time']){
                $data['female_time'] = strtotime($data['female_time']);
            }
            $competitionModel = new CompetitionModel();
            $info = $competitionModel->validate('Competition')->save($data,['id'=>input('id')]);
            if($info){
                return $this->success("修改成功",Url("Competition/index"));
            }else{
                return $this->get_update_error_msg($competitionModel->getError());
            }
        }else{
            $id = input('id');
            $msg = CompetitionModel::get($id);
            $this->assign('msg',$msg);

            return $this->fetch();
        }
    }

    /**
     * 删除
     */
    public function del(){
        $id = input('id');
        $data['status'] = '-1';
        $info = CompetitionModel::where('id',$id)->update($data);
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
        $info = CompetitionModel::where('id', 'in', $ids)->update($data);

        if ($info) {
            return $this->success('批量删除成功', url('index'));
        } else {
            return $this->error('批量删除失败');
        }
    }
}