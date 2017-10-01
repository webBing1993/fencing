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
use app\home\model\AwardBrowse;
use app\home\model\WechatUser;
use think\Db;
use think\Url;

/**
 * Class Award
 * @package app\home\controller  抽奖活动
 */
class Award extends Base
{
    public static $ENDTIME = '2017-10-15 00:00:00'; // 活动结束时间
    public static $STARTTIME = '2017-10-1 9:00:00'; // 活动开始时间

    /**
     * 答题页面
     */
    public function index()
    {
        $this->checkRole(); // 游客禁止
        $this->anonymous();
        $this->checkTime(); // 活动是否过期
        $this->assign('firstTime',0); //该时段默认不是第一次进入

        $userid = session('userId');
        $nine = strtotime(date('Y-m-d 09:00:00'));  // 当日 9:00  时间戳
        $fifteen = strtotime(date('Y-m-d 15:30:00'));  // 当日 15:30  时间戳
        $yesterday = $fifteen - 60*60*24; //昨天 15:30 时间戳

        // 当日早9点到下午3点半之间
        if (time() > $nine && time() <= $fifteen){

            $award = AwardModel::where('userid',$userid)->where('create_time',['>',$nine],['<=',$fifteen],'and')->find();
        } else if (time() > $fifteen){

            // 晚六点后
            $award = AwardModel::where('userid',$userid)->where('create_time',['>',$fifteen],'and')->select();
        } else {

            // 昨天3点半之后到早9点之前
            $award = AwardModel::where('userid',$userid)->where('create_time',['>',$yesterday],['<=',$nine],'and')->find();
        }

        if (empty($award)) {

            // 每个时间段首次进去加2积分
            $res = $this->checkFirstTime($userid);
            if ($res) {
                WechatUser::where('userid',$userid)->setInc('score', 2);
                $this->assign('firstTime',1);
            }
            // 没有数据
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
        } else {  //  有数据  获取 改用户 已经答过的题目
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
        $this->checkTime();
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
            return $this->success('提交成功',array('id'=>$res,'score'=>$score,'right'=>$Right,'award' => 1));
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
        $this->checkTime();
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
        $this->checkTime();
        $userId = session('userId');

        if (IS_POST) {
            $id = input('award_id');
            $result = Db::name('award_record')->where(['award_id' => $id,'userid' => $userId])->find();
            if ($result) {

                return $this->error('该次答题已经抽过奖!');
            } else {
                $res = $this->random($id);

                return $this->success('抽奖成功!',null,$res);
            }

        } else {
            $id = input('get.id');
            $res = AwardModel::where(['id' => $id])->find();
//            if (empty($res) || $res['score'] != 3){
//                return $this->error('抱歉~~系统参数丢掉了',Url('Award/index'));
//            }
            $result = Db::name('award_record')->where(['award_id' => $id,'userid' => $userId])->find();
            if ($result){
                $state = 1; // 已经抽奖
            }else{
                $state = 0;  // 未抽奖
            }

            $this->assign('state',$state);
            return $this->fetch();
        }


    }
    /**
     * 存储抽奖记录
     */
    public function push(){
        $this->checkRole();
        $this->checkTime();
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
    public function checkTime()
    {
        $endTime = strtotime(self::$ENDTIME);
        $startTime = strtotime(self::$STARTTIME);
        if (time() > $endTime || $startTime > time()) {
            $this->redirect('Award/null');
        }
    }

    /**
     * 检查每个时间段是不是第一次进来
     */
    public function checkFirstTime($uid)
    {
        $nine = strtotime(date('Y-m-d 09:00:00'));  // 当日 9:00  时间戳
        $fifteen = strtotime(date('Y-m-d 15:30:00'));  // 当日 15:30  时间戳
        $yesterday = $fifteen - 60*60*24;
        if (time() > $nine && time() <= $fifteen) {

            // 当日早9点到下午3点半之间
           $res = AwardBrowse::where('user_id',$uid)->where('create_time',['>',$nine],['<=',$fifteen],'and')->find();
        } else if (time() > $fifteen) {

            // 晚六点后
            $res = AwardBrowse::where('user_id',$uid)->where('create_time',['>',$fifteen],'and')->find();
        } else {
            // 昨天3点半之后到早9点之前
            $res = AwardBrowse::where('user_id',$uid)->where('create_time',['>',$yesterday],['<=',$nine],'and')->find();
        }

        if (empty($res)) {
            AwardBrowse::create(['user_id' => $uid]);

            return true;
        } else {

            return false;
        }

    }
    /**
     * @return mixed  我的奖品
     */
    public function prize(){

        return $this->fetch();
    }

    /**
     * 奖品随机抽取
     */
    private function random($id)
    {
        $nine = strtotime(date('Y-m-d 09:00:00'));  // 当日 9:00  时间戳
        $fifteen = strtotime(date('Y-m-d 15:30:00'));  // 当日 15:30  时间戳
        $yesterday = $fifteen - 60*60*24; //昨天 15:30 时间戳
        $yesterdayNine = $nine - 60*60*24; //昨天 9:00 时间戳
        $userid = session('userId');

        //先判断本时间段有没有抽到奖
        $map =[
            'userid' => $userid,
            'stuff_id' => ['lt',4],
            'type' => 1
        ];
        if (time() < $nine) {

            // 9点钟之前判断昨天9点后有没有中奖
            $map['create_time'] = ['egt',$yesterdayNine];
        } else if (time() >= $fifteen) {

            // 15点钟之前判断今天9点后有没有中奖
            $map['create_time'] = ['egt',$nine];
        }
        $result = Db::name('award_record')->where($map)->find();
        if ($result) {
            // 第一次抽到奖了 直接送个幸运奖
            $rand = 4;
        } else {
            unset($map['userid']);

            // 随机抽出奖品
            while(true){

                $rand = rand(1,200);
                // 一等奖
                if ($rand<=4) {

                    $map['stuff_id'] = 1;
                    $count = Db::name('award_record')->where($map)->count();
                    if ($count < 4) {
                        $rand = $map['stuff_id'];
                        break;
                    }
                } else if ($rand<=24) {

                    // 二等奖
                    $map['stuff_id'] = 2;
                    $count = Db::name('award_record')->where($map)->count();
                    if ($count < 20) {
                        $rand = $map['stuff_id'];
                        break;
                    }
                } else if ($rand<=104) {

                    // 三等奖
                    $map['stuff_id'] = 3;
                    $count = Db::name('award_record')->where($map)->count();
                    if ($count < 80) {
                        $rand = $map['stuff_id'];
                        break;
                    }
                } else {
                    $rand = 4;
                    break;
                }

            }
        }

        $data = array(
            'stuff_id' => $rand,
            'award_id' => $id,
            'userid' => $userid,
            'type' => 1,
            'status' => 0,
            'create_time' => time()
        );
        Db::name('award_record')->insert($data);

        // 幸运奖送10积分
        if ($rand == 4) {
            WechatUser::where('userid',$userid)->setInc('score', 10);
        }

        return $rand;
    }

}