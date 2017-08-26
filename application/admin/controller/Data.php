<?php
namespace app\admin\controller;
use app\home\model\Browse;
use think\Db;
/**
 * Created by PhpStorm.
 * User: laowang
 * Date: 2017/7/26
 * Time: 15:59
 */
class Data extends Admin
{   
    //  数据统计
    public function index(){
        $map = array(
            'status' => 1,  // 已关注
        );
        $list = $this->lists('WechatUser',$map,'id desc');
        foreach($list as $value){
            if ($value['department']){
                $Depart = json_decode($value['department']);
                $value['department'] = Db::name('wechat_department')->where('id',$Depart[0])->value('name');
            }else{
                $value['department'] = '暂无';
            }
            $map = [
                'user_id' => $value['userid'],
                'status' => ['egt',0]
            ];
            $or = [
                'study_id' => ['neq',null],
                'film_id' => ['neq',null],
                'music_id' => ['neq',null],
                'book_id' => ['neq',null],
                'special_id' => ['neq',null]
            ];
            $score_week = Browse::where($map)->whereOr($or)->whereTime('create_time','w')->count();
            $score_mouth = Browse::where($map)->whereOr($or)->whereTime('create_time','m')->count();
            $score_year = Browse::where($map)->whereOr($or)->whereTime('create_time','y')->count();
            $value['week'] = $score_week;  // 周
            $value['mouth'] = $score_mouth;  // 月
            $value['year'] = $score_year; // 年
            // 组织生活
            $ors = [
                'work_id' => ['neq',null],
                'appraise_id' => ['neq',null],
                'vote_id' => ['neq',null]
            ];
            $times = Browse::where($map)->whereOr($ors)->count();
            $value['times'] = $times;
            // 两学一做  专题模块    红色珍藏   停留时间
            
            // 在线答题
            $num1 = 0;
            $sum1 = 0;
            $arr = Db::name('answer_data')->where(['userid' => $value['userid']])->select();
            foreach($arr as $val){
                $num1 += $val['num'];
                $sum1 += $val['sum'];
            }
            // 每日一课
            $num2 = 0;
            $sum2 = 0;
            $arrs = Db::name('answers')->where(['userid' => $value['userid']])->select();
            foreach($arrs as $val){
                foreach(json_decode($val['value']) as $valu){
                    if($valu != -1){
                        $sum2 ++;
                    }
                }
                foreach(json_decode($val['status']) as $valu){
                    if($valu == 1){
                        $num2 ++;
                    }
                }
            }
            $value['num'] = $num1 + $num2;
            $value['sum'] = $sum1 + $sum2;
        }
        $this->assign('list',$list);
        return $this->fetch();
    }
}