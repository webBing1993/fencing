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
        $six_y = strtotime(date('Y-m-d 09:00:00'))-15*60*60;  // 前一天 18:00 时间戳
        $nine = strtotime(date('Y-m-d 09:00:00'));  // 当日 9:00  时间戳
        $twelve = strtotime(date('Y-m-d 12:00:00'));  // 当日 12:00  时间戳
        $six = strtotime(date('Y-m-d 18:00:00'));  // 当日 18:00  时间戳
        $nine_t = strtotime(date('Y-m-d 18:00:00'))+15*60*60;  // 次日 9:00  时间戳
        $this->check_time();
        if (time() >= $nine && time() < $twelve){
            // 早九点  到  中十二点 中间段
            $award = AwardModel::where('userid',$userid)->where('create_time',['>=',$nine],['<',$twelve],'and')->find();
        }else if (time() >= $twelve && time() < $six){
            // 中十二点  到  晚六点 中间段
            $award = AwardModel::where('userid',$userid)->where('create_time',['>=',$twelve],['<',$six],'and')->find();
        }else if (time() >= $six && time() < $nine_t){
            // 晚六点  到 次日 九点
            $award = AwardModel::where('userid',$userid)->where('create_time',['>=',$six],['<',$nine_t],'and')->find();
        }else if (time() >= $six_y && time() < $nine){
            // 作晚六点  到  今日 九点
            $award = AwardModel::where('userid',$userid)->where('create_time',['>=',$six_y],['<',$nine],'and')->find();
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
        $this->check_time();
        $id = input('get.id');
        $userId = session('userId');
        $res = AwardModel::where(['id' => $id])->find();
        if (empty($res) || $res['score'] != 3){
            return $this->error('抱歉~~系统参数丢掉了',Url('Award/index'));
        }
        $result = Db::name('award_record')->where(['award_id' => $id,'userid' => $userId])->find();
        if ($result){
            return $this->error('您已经完成抽奖,再次抽奖请先去答题~~',Url('Award/index'));
        }
        return $this->fetch();
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
}