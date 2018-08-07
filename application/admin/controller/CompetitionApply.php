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
use app\admin\model\CompetitionApply as CompetitionApplyModel;
use think\Config;

/**
 * Class CompetitionApply
 * @package  比赛报名控制器
 */
class CompetitionApply extends Admin {
    /**
     * 主页列表
     */
    public function index(){
        $map = array(
//            'status' => array('egt',0),
        );
        $search = input('search');
        if ($search != '') {
            $map['name|title'] = ['like', '%' . $search . '%'];
        }
        $type = (int)input('type');
        if ($type != '') {
            $map['type'] = $type;
        }
        $kinds = (int)input('kinds');
        if ($kinds != '') {
            $map['kinds'] = $kinds;
        }
        $status = (int)input('status');
        if (input('status') != '') {
            $map['status'] = $status;
        }else{
            $status = null;
        }
        $list = $this->lists('CompetitionApply',$map);
        $type_list = CompetitionEventModel::EVENT_TYPE_ARRAY;
        $kinds_list = CompetitionEventModel::EVENT_KINDS_ARRAY;
        $status_list = array(-1 =>"已退赛",0 =>"未支付",1=>"已支付");
        int_to_string($list,array(
            'type' => $type_list,
            'kinds' => $kinds_list,
            'status' => $status_list,
        ));

        $this->assign('type_list',$type_list);
        $this->assign('checkType', $type);
        $this->assign('kinds_list',$kinds_list);
        $this->assign('checkKinds', $kinds);
        $this->assign('status_list',$status_list);
        $this->assign('checkStatus', $status);
        $this->assign('list',$list);

        return $this->fetch();
    }

    /**
     * 查看
     */
    public function edit(){
        $id = input('id');
        $msg = CompetitionApplyModel::get($id);
        $this->assign('msg',$msg);

        return $this->fetch();
    }

    /**
     * 退赛
     */
    public function del(){
        $id = input('id');
        $data['status'] = '-1';
        $info = CompetitionApplyModel::where('id',$id)->update($data);
        if($info) {
            //TODO 支付表处理
            return $this->success("退赛成功");
        }else{
            return $this->error("退赛失败");
        }

    }

}