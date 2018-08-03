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
use app\admin\model\CompetitionEvent as CompetitionEventModel;
use app\admin\model\Competition as CompetitionModel;
use think\Config;

/**
 * Class Competition
 * @package  比赛项目控制器
 */
class CompetitionEvent extends Admin {
    /**
     * 主页列表
     */
    public function index(){
        $pid = input('pid');
        $map = array(
            'competition_id' => $pid,
            'status' => array('egt',0),
        );
        $list = $this->lists('CompetitionEvent',$map,'sort');
        int_to_string($list,array(
            'type' => CompetitionEventModel::EVENT_TYPE_ARRAY,
            'kinds' => CompetitionEventModel::EVENT_KINDS_ARRAY,
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
            $model = CompetitionEventModel::where(['competition_id' => $data['competition_id'], 'type' => $data['type'], 'kinds' => $data['kinds']])->find();
            if($model){
                return $this->error('请不要重复添加！');
            }
            $CompetitionEventModel = new CompetitionEventModel();
            $info = $CompetitionEventModel->validate('CompetitionEvent')->save($data);
            if($info) {
                return $this->success("添加成功", Url('CompetitionEvent/index', array('pid' => $data['competition_id'])));
            }else{
                return $this->error($CompetitionEventModel->getError());
            }
        }else{
            $pid = input('pid');
            $this->assign('msg','');
            $model = CompetitionModel::get($pid);
            $this->assign('pid',$pid);
            $this->assign('model',$model);
            $this->assign('event_type',CompetitionEventModel::EVENT_TYPE_ARRAY);
            $this->assign('event_kinds',CompetitionEventModel::EVENT_KINDS_ARRAY);

            return $this->fetch('edit');
        }
    }

    /**
     * 修改
     */
    public function edit(){
        if(IS_POST) {
            $data = input('post.');
            $model = CompetitionEventModel::where(['competition_id' => $data['competition_id'], 'type' => $data['type'], 'kinds' => $data['kinds']])->find();
            if($model){
                return $this->error('请不要重复添加！');
            }
            $CompetitionEventModel = new CompetitionEventModel();
            $info = $CompetitionEventModel->validate('CompetitionEvent')->save($data,['id'=>input('id')]);
            if($info){
                return $this->success("修改成功",Url("CompetitionEvent/index", array('pid' => $data['competition_id'])));
            }else{
                return $this->get_update_error_msg($CompetitionEventModel->getError());
            }
        }else{
            $pid = input('pid');
            $id = input('id');
            $msg = CompetitionEventModel::get($id);
            $model = CompetitionModel::get($pid);
            $this->assign('pid',$pid);
            $this->assign('model',$model);
            $this->assign('msg',$msg);
            $this->assign('event_type',CompetitionEventModel::EVENT_TYPE_ARRAY);
            $this->assign('event_kinds',CompetitionEventModel::EVENT_KINDS_ARRAY);

            return $this->fetch();
        }
    }

    /**
     * 删除
     */
    public function del(){
        $id = input('id');
        $data['status'] = '-1';
        $info = CompetitionEventModel::where('id',$id)->update($data);
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
        $info = CompetitionEventModel::where('id', 'in', $ids)->update($data);

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
                $result = CompetitionEventModel::where('id', $value)->update(['sort' => $key+1]);
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

            $list = CompetitionEventModel::all(function($query) use($map){
                $query->where($map)->field('id,type,kinds')->order('sort asc,id asc');
            });
            $this->assign('pid', $pid);
            $this->assign('list', $list);

            return $this->fetch();
        }
    }
}