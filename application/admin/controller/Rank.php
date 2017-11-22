<?php
/**
 * Created by PhpStorm.
 * User: laowang <958364865@qq.com>
 * Date: 2017/9/11
 * Time: 16:14
 */

namespace app\admin\controller;
use app\admin\model\WechatDepartment;
use app\admin\model\WechatDepartmentUser;
use app\admin\model\WechatUserTag;
use app\admin\model\WechatUser;
use think\Db;
/**
 * Class Rank
 * @package app\admin\controller  排行榜
 */
class Rank extends Admin
{
    /**
     * 首页
     */
    public function index(){
        if (IS_POST){

        }else{
            $mouth = date('m',time());  // 当前月份
            // 获取签到人员列表
            $map = array(
                'type' => 2,
                'status' => 0,
                'mouth' => $mouth
            );
            $list = Db::name('apply')->field('userid,sum(score) as sums')->where($map)->group('userid')->select();
            foreach($list as $key => $value){
                // 干预分数
                $info = Db::name('handle')->where(['userid' => $value['userid'],'mouth' => $mouth])->select();
                $sum = 0;
                foreach($info as $v){
                    $sum += $v['score'];
                }
                $list[$key]['sum'] = $value['sums'] + $sum;
                $list[$key]['mouth'] = $mouth;
                $User = WechatUser::where('userid',$value['userid'])->find();
                if ($User){
                    $list[$key]['name'] = $User['name'];
                    $department_id = WechatDepartmentUser::where('userid',$value['userid'])->value('departmentid');
                    $list[$key]['department'] = WechatDepartment::where('id',$department_id)->value('name');
                    //基础分
                    $list[$key]['base'] = $User['volunteer_base'];
                }else {
                    $list[$key]['name'] = '暂无';
                    $list[$key]['department'] = "暂无";
                    //基础分
                    $list[$key]['base'] = 0;
                }
            }
            $this->assign('list',$list);
            return $this->fetch();
        }
    }
    /**
     * 积分详情
     */
    public function detail($mouth,$userid){
        $map = array(
            'type' => 2,
            'mouth' => $mouth,
            'userid' => $userid,
        );
        $list = $this->lists('Apply',$map);
        foreach($list as $value){
            $info = Db::name('work')->where('id',$value['sign_id'])->find();
            $value['title']  = $info['title'];
        }
        $this->assign('list',$list);
        return  $this->fetch();
    }
    /**
     * 操作日志
     */
    public function book(){
        $search = input('search');
        $where = array();
        if ($search != '') {
            $map['name'] = ['like','%'.$search.'%'];
            $arr = WechatUser::where($map)->column('userid');
            $where = array(
                'userid' => ['in',$arr]
            );
        }
        $list =  $this->lists('Handle',$where);
        foreach($list as $value){
            $name = WechatUser::where('userid',$value['userid'])->value('name');
            switch ($value['class']){
                case 1:
                    $pre = "党风廉政";
                    $num = 1;
                    break;
                case 2:
                    $pre = "满意度测评积分";
                    $num = 1;
                    break;
                case 3:
                    $pre = "两新党建";
                    $num = 1;
                    break;
            }
            if ($value['type'] == 1){
                $ps = "减去";
            }else{
                $ps = "增加";
            }
            $value['content'] = "对用户：【".$name."】的【".$pre.'】【'.$ps."】".$num." 分";
        }
        $this->assign('list',$list);
        return $this->fetch();
    }
    /**
     * 人工操作积分
     */
    public function handle(){
        $data = input('post.');
        $userid = $data['userid'];
        $class = $data['class'];  // 1 党风廉政 4分 2 满意度测评 3 两新党建
        $type = $data['type'];  // 1 减  2 加
        $User = WechatUser::where('userid',$userid)->find();
        if (empty($User)){
            return $this->error('操作失败');
        }
        switch ($class){
            case 1:
                $field = 'score_party';
                $num = 1;
                break;
            case 2:
                $field = 'score_satisfaction';
                $num = 1;
                break;
            case 3:
                $field = 'score_work';
                $num = 1;
                break;
        }
        $create_user = $_SESSION['think']['user_auth']['id'];
        if ($type == 1){
            // 减分
            $res = WechatUser::where('userid',$userid)->setDec($field,$num);
        }else{
            // 加分
            $res = WechatUser::where('userid',$userid)->setInc($field,$num);
        }
        if ($res){
            // 存日志
            Db::name('handle')->insert(['userid' => $userid,'class' => $class,'type' => $type,'create_time' => time(),'create_user' => $create_user]);
            return  $this->success('操作成功');
        }else{
            return $this->error('操作失败');
        }
    }
}