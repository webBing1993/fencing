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
use app\admin\model\CompetitionGroup as CompetitionGroupModel;
use app\admin\model\Competition as CompetitionModel;
use think\Config;

/**
 * Class Competition
 * @package  比赛组别控制器
 */
class CompetitionGroup extends Admin {
    /**
     * 主页列表
     */
    public function index(){
        $pid = input('pid');
        $map = array(
            'competition_id' => $pid,
            'status' => array('egt',0),
        );
        $list = $this->lists('CompetitionGroup',$map,'sort');
        int_to_string($list,array(
            'status' => array(0 =>"已发布"),
        ));
        $name = CompetitionModel::where('id', $pid)->value('title');

        $this->assign('list',$list);
        $this->assign('pid',$pid);
        $this->assign('name',$name);

        return $this->fetch();
    }

    /**
     * 添加
     */
    public function add(){
        if(IS_POST) {
            $data = input('post.');
            if(empty($data['id'])){
                unset($data['id']);
            }
            if($data['end_time'] < $data['start_time']){
                return $this->error("结束时间必须大于开始时间");
            }
            if($data['start_time']){
                $data['start_time'] = strtotime($data['start_time']);
            }
            if($data['end_time']){
                $data['end_time'] = strtotime($data['end_time']);
            }
            $competitionGroupModel = new CompetitionGroupModel();
            $info = $competitionGroupModel->validate('CompetitionGroup')->save($data);
            if($info) {
                return $this->success("添加成功", Url('CompetitionGroup/index', array('pid' => $data['competition_id'])));
            }else{
                return $this->error($competitionGroupModel->getError());
            }
        }else{
            $pid = input('pid');
            $this->assign('msg','');
            $model = CompetitionModel::get($pid);
            $this->assign('pid',$pid);
            $this->assign('model',$model);

            return $this->fetch('edit');
        }
    }

    /**
     * 修改
     */
    public function edit(){
        if(IS_POST) {
            $data = input('post.');
            if($data['end_time'] < $data['start_time']){
                return $this->error("结束时间必须大于开始时间");
            }
            if($data['start_time']){
                $data['start_time'] = strtotime($data['start_time']);
            }
            if($data['end_time']){
                $data['end_time'] = strtotime($data['end_time']);
            }
            $competitionGroupModel = new CompetitionGroupModel();
            $info = $competitionGroupModel->validate('CompetitionGroup')->save($data,['id'=>input('id')]);
            if($info){
                return $this->success("修改成功",Url("CompetitionGroup/index", array('pid' => $data['competition_id'])));
            }else{
                return $this->get_update_error_msg($competitionGroupModel->getError());
            }
        }else{
            $pid = input('pid');
            $id = input('id');
            $msg = CompetitionGroupModel::get($id);
            $model = CompetitionModel::get($pid);
            $this->assign('pid',$pid);
            $this->assign('model',$model);
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
        $info = CompetitionGroupModel::where('id',$id)->update($data);
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
        $info = CompetitionGroupModel::where('id', 'in', $ids)->update($data);

        if ($info) {
            return $this->success('批量删除成功', url('index'));
        } else {
            return $this->error('批量删除失败');
        }
    }

    /**
     * 排序
     */
    public function sort(){
        if (IS_POST){
            $ids = input('ids');
            $ids = explode(',', $ids);
            foreach ($ids as $key=>$value){
                $result = CompetitionGroupModel::where('id', $value)->update(['sort' => $key+1]);
            }
            if($result !== false){
                return $this->success('排序成功！');
            }else{
                return $this->error('排序失败！');
            }
        } else {
            $pid = input('pid');
            //获取排序的数据
            $map = array('status'=>array('gt',-1));
            if($pid) $map['competition_id'] = $pid;

            $list = CompetitionGroupModel::all(function($query) use($map){
                $query->where($map)->field('id,group_name')->order('sort asc,id asc');
            });
            $this->assign('pid', $pid);
            $this->assign('list', $list);

            return $this->fetch();
        }
    }
}