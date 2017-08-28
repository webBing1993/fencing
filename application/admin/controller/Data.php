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
            $map1 = [
                'user_id' => $value['userid'],
                'status' => ['egt',0],
                'study_id' => array('exp',"is not null"),
            ];
            $map2 = [
                'user_id' => $value['userid'],
                'status' => ['egt',0],
                'film_id' => array('exp',"is not null"),
            ];
            $map3 = [
                'user_id' => $value['userid'],
                'status' => ['egt',0],
                'music_id' => array('exp',"is not null"),
            ];
            $map4 = [
                'user_id' => $value['userid'],
                'status' => ['egt',0],
                'book_id' => array('exp',"is not null"),
            ];
            $map5 = [
                'user_id' => $value['userid'],
                'status' => ['egt',0],
                'special_id' => array('exp',"is not null"),
            ];
            $value['week'] = $this->get_count($map1,'w') + $this->get_count($map2,'w') + $this->get_count($map3,'w') + $this->get_count($map4,'w') + $this->get_count($map5,'w');  // 周
            $value['mouth'] = $this->get_count($map1,'m') + $this->get_count($map2,'m') + $this->get_count($map3,'m') + $this->get_count($map4,'m') + $this->get_count($map5,'m');  // 月
            $value['year'] = $this->get_count($map1,'y') + $this->get_count($map2,'y') + $this->get_count($map3,'y') + $this->get_count($map4,'y') + $this->get_count($map5,'y'); // 年
            // 组织生活
            $maps1 = [
                'user_id' => $value['userid'],
                'status' => ['egt',0],
                'work_id' => array('exp',"is not null"),
            ];
            $maps2 = [
                'user_id' => $value['userid'],
                'status' => ['egt',0],
                'appraise_id' => array('exp',"is not null"),
            ];
            $maps3 = [
                'user_id' => $value['userid'],
                'status' => ['egt',0],
                'vote_id' => array('exp',"is not null")
            ];
            $times1 = Browse::where($maps1)->count();
            $times2 = Browse::where($maps2)->count();
            $times3 = Browse::where($maps3)->count();
            $value['times'] = $times1 + $times2 + $times3;
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
    // 获取 数量
    public function get_count($where='',$type='d'){
        return  Browse::where($where)->whereTime('create_time',$type)->count();
    }
}