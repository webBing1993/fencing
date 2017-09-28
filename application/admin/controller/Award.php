<?php
/**
 * Created by PhpStorm.
 * User: laowang <958364865@qq.com>
 * Date: 2017/9/27
 * Time: 13:47
 */

namespace app\admin\controller;
use app\admin\model\AwardStuff;
use app\admin\model\AwardRecord;
/**
 * Class Award
 * @package app\admin\controller 答题抽奖控制器
 */
class Award extends Admin
{
    /**
     * 奖品管理 主页
     */
    public function index(){
        $map = array(
            'status' => 0,
        );
        $list = $this->lists('AwardStuff',$map);
        foreach($list as $value){
            $res = AwardRecord::where(['stuff_id' => $value['id'],'status' =>0])->count();
            $value['remain'] = $value['sum'] - $res;
        }
        int_to_string($list,array(
            'type' => array(0 => "常规奖品",1 => "终极奖品"),
            'status' => array( 0 => "已发布"),
        ));
        $this->assign('list',$list);
        return $this->fetch();
    }
    /*
     * 产品  添加/修改
     */
    public function edit(){
        if (IS_POST){
            // 保存
            $data = input('post.');
            $Product = new AwardStuff();
            $data['create_user'] = $_SESSION['think']['user_auth']['id'];
            if (empty($data['id'])){
                unset($data['id']);
                $model = $Product->validate('Award')->save($data);
            }else{
                $model = $Product->validate('Award')->save($data,['id' => $data['id']]);
            }
            if($model){
                return $this->success('操作成功',Url("Award/index"));
            }else{
                return $this->error($Product->getError());
            }
        }else{
            input('get.id') ? $id = input('get.id') : $id = 0;
            $msg = AwardStuff::where(['id' => $id])->find();
            $this->assign('msg',$msg);
            return $this->fetch();
        }
    }
    /*
     * 奖品 删除
     */
    public function del(){
        $id = input('id');
        if (empty($id)){
            return $this->error("系统参数错误");
        }
        $map['status'] = "-1";
        $info = AwardStuff::where('id',$id)->update($map);
        if($info) {
            $Car = AwardRecord::where('stuff_id',$id)->find();
            if (empty($Car)){
                return $this->success("删除成功");
            }else{
                $res = AwardRecord::where('product_id',$id)->update($map);
                if ($res){
                    return $this->success("删除成功");
                }else{
                    return $this->error("删除失败");
                }
            }
        }else{
            return $this->error("删除失败");
        }
    }
    /**
     * 获奖记录  主页
     */
    public function record(){
        $map = array(
            'status' => 0,
        );
        $list = $this->lists('AwardRecord',$map);
        foreach($list as $value){
            $type = AwardStuff::where(['id' => $value['stuff_id'],'status' => 0])->value('type');
            if ($type == 0){
                $value['type'] = "常规奖品";
            }else{
                $value['type'] = "终极奖品";
            }
            $value['name'] = AwardStuff::where(['id' => $value['stuff_id'],'status' => 0])->value('name');
        }
        $this->assign('list',$list);
        return $this->fetch();
    }
    /**
     * 获奖记录 删除
     */
    public function dele(){
        $id = input('id');
        if (empty($id)){
            return $this->error("系统参数错误");
        }
        $map['status'] = "-1";
        $res = AwardRecord::where('id',$id)->update($map);
        if ($res){
            return $this->success("删除成功");
        }else{
            return $this->error("删除失败");
        }
    }
    /**
     * 参与记录  主页
     */
    public function join(){
        $list = $this->lists('Award');
        foreach($list as $value){
            $value['question'] = implode(",",json_decode($value['question_id']));
        }
        $this->assign('list',$list);
        return $this->fetch();
    }
}