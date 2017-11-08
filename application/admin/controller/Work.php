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
use app\admin\model\Work as WorkModel;
use think\Db;

/**
 * Class Work
 * @package app\admin\controller  签到管理  控制器
 */
class Work extends Admin
{
    /*
     * 主页
     */
    public function index(){
        $map = array(
            'type' => 1,
            'status' => array('egt',0),
        );
        $list = $this->lists('Work',$map);
        $this->assign('list',$list);
        return $this->fetch();
    }
    /*
     *  删除
     */
    public function dele(){
        $id = input('id');
        if (empty($id)){
            return $this->error('系统参数错误');
        }
        $res = WorkModel::where('id',$id)->update(['status' => -1]);
        if ($res){
            return $this->success('删除成功');
        }else{
            return $this->error('删除失败');
        }
    }
    /*
     * 添加  修改
     */
    public function add(){

    }
}