<?php
namespace app\home\controller;
/**
 * Created by PhpStorm.
 * User: 老王
 * Date: 2017/7/18
 * Time: 9:19
 */
use app\home\model\Special as SpecialModel;
// 专题 模块
class Special extends Base
{
  /*
   * 专题模块 列表
   */
    public function index(){
        // 主题
        $map = array(
            'type' => 1,
            'status' => ['egt',0]
        );
        $topic = SpecialModel::where($map)->find();
        $this->assign('topic',$topic);
        // 专题列表
        $maps = array(
            'type' => 2,
            'status' => ['egt',0]
        );
        $list = SpecialModel::where($maps)->order('id desc')->limit(7)->select();
        $this->assign('list',$list);
        return $this->fetch();
    }
    /*
     * 专题 详情
     */
    public function detail(){
        return $this->fetch();
    }
    /*
     * 专题 列表 更多
     */
    public function more(){

    }
}