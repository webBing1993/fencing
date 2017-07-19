<?php
namespace app\admin\controller;
/**
 * Created by PhpStorm.
 * User: 老王
 * Date: 2017/7/18
 * Time: 8:58
 */
use app\admin\model\WechatDepartment;
use app\admin\model\Appraise;
use app\admin\model\AppraiseOptions;
use app\admin\model\WechatUser;
use app\admin\model\AppraiseAnswer;
use think\Db;

class Work extends Admin
{
    /*
     * 三会一课
     */
    public function index(){
        $map = array(
            'type' => 1,
            'status' => array('egt',0),
        );
        $list = $this->lists('Work',$map);
        int_to_string($list,array(
            'status' => array(0 =>"已发布"),
        ));
        $this->assign('list',$list);
        return $this->fetch();
    }
    /*
     * 支部活动
     */
    public function activity(){

    }
    /*
     * 民主评议
     */
    public function appraise(){
        $map = array(
            'status' => array('egt',0),
        );
        $list = $this->lists('Appraise',$map);
        foreach($list as $value){
            $Department = WechatDepartment::where('id',$value['publisher'])->field('name')->find();
            $value['publisher'] = $Department['name'];
        }
        int_to_string($list,array(
            'status' => array(0=>"已发布",1=>"已发布"),
        ));
        $this->assign('list',$list);
        return $this->fetch();
    }
    /*
     * 民主评议 主题  添加  修改
     */
    public function add(){
        $id = input('get.id');
        if ($id){
            // 修改
            if(IS_POST){
                //  修改 保存
                $data = input('post.');
                if ($data['publisher'] == -1){
                    return $this->error('请选择发布支部');
                }
                $Vote = new Appraise();
                // 添加保存 题目
                $model = $Vote->validate('Appraise.act')->save($data,['id' => $id]);
                if($model){
                    // 添加保存 选项
                    return $this->success("修改主题成功",Url('Work/appraise'));
                }else{
                    return $this->error($Vote->getError());
                }
            }else{
                $Department = WechatDepartment::where(['parentid' => ['neq',0]])->field('id,name')->select();
                $this->assign('info',$Department);
                $Vote = Appraise::where('id',$id)->find();
                $this->assign('msg',$Vote);
                return  $this->fetch();
            }
        }else{
            //添加
            if(IS_POST){
                //  添加 保存
                $data = input('post.');
                if ($data['publisher'] == -1){
                    return $this->error('请选择发布支部');
                }
                $Special = new Appraise();
                // 添加保存 题目
                $model = $Special->validate('Appraise.act')->save($data);
                if($model){
                    // 添加保存 选项
                    return $this->success("新增主题成功",Url('Work/appraise'));
                }else{
                    return $this->error($Special->getError());
                }
            }else{
                $Department = WechatDepartment::where(['parentid' => ['neq',0]])->field('id,name')->select();
                $this->assign('info',$Department);
                $this->assign('msg','');
                return  $this->fetch();
            }
        }
    }
    /*
     * 民主评议  主题  删除
     */
    public function del(){
        $id = input('id');
        $info['status'] = -1;
        $res = Appraise::where('id',$id)->update($info);  // 删除投主题
        if ($res){
            $num = AppraiseOptions::where(['app_id' => $id,'status' => 0])->count();  // 删除相应的选项
            if ($num == 0){
                return $this->success('删除成功');
            }else{
                $result = AppraiseOptions::where('app_id',$id)->update($info);
                if ($result){
                    return $this->success('删除成功');
                }else{
                    return $this->error('删除失败');
                }
            }
        }else{
            return $this->error('删除失败');
        }
    }
    /*
     * 选项详情 列表
     */
    public function options(){
        $id = input('id');
        $map = array(
            'app_id' => $id,
            'status' => array('egt',0),
        );
        $list = $this->lists('AppraiseOptions',$map,['num'=>'desc']);
        int_to_string($list,array(
            'status' => array(0=>"已发布"),
        ));
        $this->assign('list',$list);
        $this->assign('vid',$id);  // 投票主题id
        return $this->fetch();
    }
    /*
     * 投票选项  添加 / 修改
     */
    public function edit(){
        $id = input('param.id');
        $app_id = input('param.vid');
        if ($id){
            // 修改
            if(IS_POST){
                //  添加 保存
                $data = input('post.');
                $Options = new AppraiseOptions();
                // 添加保存 题目
                $model = $Options->validate('Appraise.other')->save($data,['id' => $id]);
                if($model){
                    // 添加保存 选项
                    return $this->success("修改选项成功",Url('Work/options?id='.$app_id));
                }else{
                    return $this->error($Options->getError());
                }
            }else{
                $Options = AppraiseOptions::where('id',$id)->find();
                $this->assign('msg',$Options);
                return  $this->fetch();
            }
        }else{
            //添加
            if(IS_POST){
                //  添加 保存
                $data = input('post.');
                $data['app_id'] = $app_id;
                $Options = new AppraiseOptions();
                // 添加保存 题目
                $model = $Options->validate('Vote.other')->save($data);
                if($model){
                    // 添加保存 选项
                    return $this->success("新增选项成功",Url('Work/options?id='.$app_id));
                }else{
                    return $this->error($Options->getError());
                }
            }else{
                $this->assign('msg','');
                return  $this->fetch();
            }
        }
    }
    /*
     * 选项删除
     */
    public function optionsdel(){
        $id = input('id');
        $info['status'] = -1;
        $res = AppraiseOptions::where('id',$id)->update($info);  // 删除选项
        if ($res){
            $result = AppraiseAnswer::where('op_id',$id)->update($info);
            if ($result){
                return $this->success('删除成功');
            }else{
                return $this->error('删除失败');
            }
        }else{
            return $this->error('删除失败');
        }
    }
    /*
     * 查看 评论详情
     */
    public function comment(){
        $id = input('post.id');
        $data = AppraiseAnswer::where(['op_id' => $id,'status' => 0])->order('id desc')->select();
        foreach($data as $value){
            $value['create_time'] = date('Y-m-d',$value['create_time']);
            $User = WechatUser::where('userid',$value['userid'])->field('name')->find();
            $value['name'] = $User['name'];
        }
        return $data;
    }
}