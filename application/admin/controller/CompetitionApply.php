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
            $map['title'] = ['like', '%' . $search . '%'];
        }
        $list = $this->lists('CompetitionApply',$map);
        int_to_string($list,array(
            'type' => CompetitionEventModel::EVENT_TYPE_ARRAY,
            'kinds' => CompetitionEventModel::EVENT_KINDS_ARRAY,
            'status' => array(-1 =>"已退赛",0 =>"未支付",1=>"已支付"),
        ));

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