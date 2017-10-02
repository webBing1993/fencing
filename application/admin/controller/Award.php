<?php
/**
 * Created by PhpStorm.
 * User: laowang <958364865@qq.com>
 * Date: 2017/9/27
 * Time: 13:47
 */

namespace app\admin\controller;
use app\admin\model\AwardRecord;
/**
 * Class Award
 * @package app\admin\controller 答题抽奖控制器
 */
class Award extends Admin
{
    /**
     * 获奖记录  主页
     */
    public function record(){
        $map = array(
            'status' => 0,
        );
        $list = $this->lists('AwardRecord',$map);

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