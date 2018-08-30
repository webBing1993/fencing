<?php
/**
 * Created by PhpStorm.
 * User: 王泽锋
 * Date: 2018/07/23
 * Time: 14:50
 */

namespace app\admin\controller;

use app\admin\model\Shop as ShopModel;
use app\admin\model\MallOne;
use app\admin\model\MallTwo;

/**
 * Class Shop
 * @package  购物商城 商品管理
 */
class Shop extends Admin {
    /**
     * 商品主页
     */
    public function index(){
        $map = array(
            'status' => array('egt',0),
        );
        $search = input('search');
        if ($search != '') {
            $map['title'] = ['like', '%' . $search . '%'];
        }
        $list = $this->lists('Shop',$map);
        int_to_string($list,array(
            'status' => array(0=>"已发布"),
        ));

        $this->assign('list',$list);
        return $this->fetch();
    }
    /**
     *  添加
     */
    public function indexadd(){
        if(IS_POST){
            $data = input('post.');
            if(empty($data['id'])) {
                unset($data['id']);
            }
            $noticeModel = new ShopModel();
            $data['create_time'] = time();
//            $data['create_user'] = $_SESSION['think']['user_auth']['id'];
            $model = $noticeModel->validate('Shop')->save($data);
            if($model){
                return $this->success('添加成功!',Url("Shop/index"));
            }else{
                return $this->error($noticeModel->getError());
            }
        }else{
            $this->assign('msg','');
            $tp1 = MallOne::where('status',0)->select();
            $tp2 = MallTwo::where('status',0)->select();
            $this->assign('tp1',$tp1);
            $this->assign('tp2',$tp2);

            return $this->fetch();
        }
    }
    /**
     * 商品 修改
     */
    public function indexedit(){
        if(IS_POST){
            $data = input('post.');
            $noticeModel = new ShopModel();
            $model = $noticeModel->validate('Shop2')->save($data,['id'=>input('id')]);
            if($model){
                return $this->success('修改成功!',Url("Shop/index"));
            }else{
                return $this->get_update_error_msg($noticeModel->getError());
            }
        }else{
            $tp1 = MallOne::where('status',0)->select();
            $tp2 = MallTwo::where('status',0)->select();
            $this->assign('tp1',$tp1);
            $this->assign('tp2',$tp2);
            //根据id获取课程
            $id = input('id');
            if(empty($id)){
                return $this->error("系统错误,不存在该条数据!");
            }else{
                $msg = ShopModel::get($id);
                $this->assign('msg',$msg);
            }

            return $this->fetch();
        }
    }
    /**
     * 商品删除
     */
    public function del(){
        $id = input('id');
        $map['status'] = "-1";
        $info = ShopModel::where('id',$id)->update($map);
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
        $info = ShopModel::where('id', 'in', $ids)->update($data);

        if ($info) {
            return $this->success('批量删除成功');
        } else {
            return $this->error('批量删除失败');
        }
    }

}