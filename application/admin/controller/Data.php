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
        $name = input('name');
        $map = ['name' => ['like', "%$name%"],'status' => 1];
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
                'study_id' => ['eq','not null'],
                'film_id' => ['eq','not null'],
                'music_id' => ['eq' ,'not null'],
                'book_id' => ['eq' ,'not null'],
                'special_id' => ['eq' ,'not null']
            ];
            $score_week = Browse::where($map)->whereTime('create_time','w')->count();
            $score_mouth = Browse::where($map)->whereTime('create_time','w')->count();
            $score_year = Browse::where($map)->count();
            $value['week'] = $score_week;  // 周
            $value['mouth'] = $score_mouth;  // 月
            $value['year'] = $score_year; // 年
            // 组织生活
            $ors = [
                'work_id' => ['eq','not null'],
                'appraise_id' => ['eq','not null'],
                'vote_id' => ['eq','not null']
            ];
            $times = Browse::where($map)->whereOr($ors)->count();
            $value['times'] = $times;
            // 两学一做  专题模块    红色珍藏   停留时间
            $stay = Db::name('stay_time')->where(['userid' => $value['userid']])->select();
            $stay_time = 0;
            foreach($stay as $vals){
                $stay_time += ($vals['end_time'] - $vals['start_time']);
            }
            $value['stay_time'] = $this->time2string($stay_time);;
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
    // 输入秒数换算成多少天/多少小时/多少分/多少秒的字符串
    function time2string($second){
        $day = floor($second/(3600*24));
        $second = $second%(3600*24);//除去整天之后剩余的时间
        $hour = floor($second/3600);
        $second = $second%3600;//除去整小时之后剩余的时间
        $minute = floor($second/60);
        $second = $second%60;//除去整分钟之后剩余的时间
        //返回字符串
        return $day.'天'.$hour.'小时'.$minute.'分'.$second.'秒';
    }
}