<?php
/**
 * Created by PhpStorm.
 * User: laowang <958364865@qq.com>
 * Date: 2017/9/26
 * Time: 16:15
 */

namespace app\home\controller;
use app\admin\model\Question;
use app\home\model\Award as AwardModel;
use app\home\model\WechatUser;
use think\Db;
use think\Url;

/**
 * Class Award
 * @package app\home\controller  抽奖活动
 */
class Award extends Base
{
    /**
     * 答题页面
     */
    public function index(){
        $this->checkRole();
        $this->anonymous();
        $userid = session('userId');
        $nine_m = strtotime(date('Y-m-d 09:00:00'));  // 当日 9:00  时间戳
        $twelve = strtotime(date('Y-m-d 12:00:00'));  // 当日 12:00  时间戳
        $six = strtotime(date('Y-m-d 18:00:00'));  // 当日 18:00  时间戳
        $nine_e = strtotime(date('Y-m-d 21:00:00'));  // 当日 21:00  时间戳
        $this->check_time();
        if (time() > $nine_m && time() <= $twelve){
            // 早九点  到  中十二点 中间段
            $award = AwardModel::where('userid',$userid)->where('create_time',['>',$nine_m],['<=',$twelve],'and')->find();
        }else if (time() > $six && time() <= $nine_e){
            // 晚六点  到 次日 九点
            $award = AwardModel::where('userid',$userid)->where('create_time',['>',$six],['<=',$nine_e],'and')->find();
        }else{
            // 其他时间段
            $this->redirect('Award/null');
        }
        if(empty($award)){   // 没有数据
            $ques = AwardModel::where('userid',$userid)->select();
            $ars = array();
            foreach($ques as $value){
                if ($value['question_id']){
                    $tem = json_decode($value['question_id']);
                    foreach($tem as $val){
                        array_push($ars,$val);
                    }
                }
            }
            //取单选
            $arr=Question::all(['type'=>0]);
            foreach($arr as $value){
                $ids[]=$value->id;
            }
            //随机获取单选的题目
            $num=3;//题目数目
            $data=array();
            while(true){
                if(count($data) == $num){
                    break;
                }
                $index=mt_rand(0,count($ids)-1);
                $res=$ids[$index];
                if ($ars){
                    if(!in_array($res,$data) && !in_array($res,$ars)){
                        $data[]=$res;
                    }
                }else{
                    if(!in_array($res,$data)){
                        $data[]=$res;
                    }
                }
            }
            foreach($data as $value){
                $question[]=Question::get($value);
            }
            $this->assign('question',$question);
            return $this->fetch();
        }else{  //  有数据  获取 改用户 已经答过的题目
            $Qid = json_decode($award->question_id);
            $rights=json_decode($award->value);
            $re = array();
            foreach($Qid as $key => $value){
                $re[$key] = Question::get($value);
                $re[$key]['right'] = $rights[$key];
            }
            $this->assign('question',$re);
            return $this->fetch('scan');
        }
    }
    /*
     * 每日一课 提交
     */
    public function commit(){
        $this->checkRole();
        $this->check_time();
        // 获取用户提交数据
        $data = input('post.');
        $arr = $data['data'];
        $question = array();
        $status = array();
        $options = array();
        $Right = array();
        $score = 0;
        foreach($arr as $key => $value){
            $Question=Question::get($value[0]);
            switch($Question->value){
                case 1:
                    $right = "A";
                    break;
                case 2:
                    $right = "B";
                    break;
                case 3:
                    $right = "C";
                    break;
                case 4:
                    $right = "D";
                    break;

            }
            $Right[$key+1] = $right;
            $question[$key] = $value[0];
            $options[$key] = $value[1];
            // 判断 题目的对错
            if($value[1] == $Question->value){
                $status[$key] = 1;
                $score ++;
            }else{
                $status[$key] = 0;
            }
        }
        if ($score == 3){
            $award = 1; // 可以抽奖
        }else{
            $award = 0;  // 不能抽奖
        }
        //将获取的数据进行json格式转化
        $questions = json_encode($question);
        $rights = json_encode($options);
        $status = json_encode($status);
        $users = session('userId');
        //将分数添加至用户积分排名
        $wechatModel = new WechatUser();
        $wechatModel->where('userid',$users)->setInc('score',2);
        //  存储 表
        $Answers = new AwardModel();
        $Answers->userid = $users;
        $Answers->question_id = $questions;
        $Answers->value = $rights;
        $Answers->status = $status;
        $Answers->score = $score;
        $Answers->create_time = time();
        $res = $Answers->save();
        if($res){
            return $this->success('提交成功',array('id' =>$res,'score'=>$score,'right'=>$Right,'award' => $award));
        }else{
            return $this->error('提交失败');
        }
    }
    /*
    * 每日一课  查看详情
    */
    public function scan(){
        $this->checkRole();
        $this->anonymous();
        $this->check_time();
        $id = input('id');
        if (empty($id)){
            return $this->error('系统错误');
        }
        $Answers = AwardModel::get($id);
        $Qid = json_decode($Answers->question_id);
        $rights=json_decode($Answers->value);
        $re = array();
        foreach($Qid as $key => $value){
            $re[$key] = Question::get($value);
            $re[$key]['right'] = $rights[$key];
        }
        $this->assign('question',$re);
        return $this->fetch();
    }
    /**
     * 抽奖 页面
     */
    public function award(){
        $this->checkRole();
        $this->check_time();
        $id = input('get.id');
        $userId = session('userId');
        $res = AwardModel::where(['id' => $id])->find();
        if (empty($res) || $res['score'] != 3){
            return $this->error('抱歉~~系统参数丢掉了',Url('Award/index'));
        }
        $result = Db::name('award_record')->where(['award_id' => $id,'userid' => $userId])->find();
        if ($result){
            $state = 1; // 已经抽奖
        }else{
            $state = 0;  // 未抽奖
        }
        // 概率计算
        $list = Db::name('award_stuff')->where(['type' => 0,'status' => 0])->field('id,sum')->select();
        $arr = array();
        foreach($list as $value){
            // 已经选中的奖品
            $res = Db::name('award_record')->where(['userid' => $userId,'stuff_id' => $value['id']])->find();
            if (!$res){
                //  剩余数量偏多的  概率大一点
                $sum = Db::name('award_record')->where(['stuff_id' => $value['id'],'status' =>0])->count();
                $num = $value['sum'] - $sum;
                if ($num > 10){
                    for($i=0;$i<100;$i++){
                        array_push($arr,$value['id']);
                    }
                }elseif ($num >5 && $num <= 10){
                    //  剩余数量偏少的  概率小一点
                    for($k=0;$k<50;$k++){
                        array_push($arr,$value['id']);
                    }
                }elseif ($num >1 && $num <= 5){
                    for($k=0;$k<20;$k++){
                        array_push($arr,$value['id']);
                    }
                }
            }else{
                array_push($arr,$value['id']);
            }
        }
        shuffle($arr); // 随机打乱数组
        $index = rand(0,count($arr)-1); // 随机索引
        $stuff_id = $arr[$index];
        $this->assign('stuff_id',$stuff_id);
        $this->assign('award_id',$id);
        $this->assign('state',$state);
        return $this->fetch();
    }
    /**
     * 存储抽奖记录
     */
    public function push(){
        $this->checkRole();
        $this->check_time();
        $userId = session('userId');
        $stuff_id = input('post.stuff_id/d'); // 奖品id
        $award_id = input('post.award_id/d');  // 答题记录id
        if (empty($stuff_id) || empty($award_id)){
            return $this->error('抱歉~~系统参数丢掉了',Url('Award/index'));
        }
        $map = array(
            'stuff_id' => $stuff_id,
            'award_id' => $award_id,
            'userid' => $userId,
            'status' => 0
        );
        $res =  $sum = Db::name('award_record')->where($map)->find();
        if (empty($res)){
            $data = array(
                'stuff_id' => $stuff_id,
                'award_id' => $award_id,
                'userid' => $userId,
                'status' => 0,
                'create_time' => time()
            );
            Db::name('award_record')->insert($data);
        }
    }
    /**
     * @return mixed  活动结束页面
     */
    public function null(){
        return $this->fetch();
    }
    /**
     * 检查活动是否结束
     */
    public function check_time(){
        $date = Db::name('award')->order('id asc')->value('create_time');
        if (!empty($date)){
            $time = strtotime(date('Y-m-d',time()));
            $first = strtotime(date('Y-m-d',$date)) + 10*24*60*60;
            if ($time > $first){
                $this->redirect('Award/null');
            }
        }
    }

    /**
     * @return mixed  我的奖品
     */
    public function prize(){

        return $this->fetch();
    }

}