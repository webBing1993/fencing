<?php
/**
 * Created by PhpStorm.
 * User: 老王
 * Date: 2017/1/3
 * Time: 13:11
 */
 namespace app\home\controller;
use app\home\model\Vote as VoteModel;
use app\home\model\VoteOptions;
use app\home\model\Browse;
use app\home\model\VoteAnswer;
use app\home\model\Picture;
/*
 * 选举投票主页
*/

class Vote extends Base{
     /*
      * 投票主页
      */
     public function index(){
         $this->jssdk();
         $map = array(
             'status' => array('eq',0),
         );
         $voteModel = new VoteModel();
         $top = $voteModel->where($map)->order('id desc')->limit(3)->select();
         $list = $voteModel->where($map)->order('id desc')->limit(5)->select();
         foreach($list as $value){
             $Options = VoteOptions::where(['vote_id' => $value->id , 'status' => 0])->select();
             $sum = 0;
             foreach($Options as $val){
                 $sum += $val->num;
             }
             $value['sum'] = $sum;
         }
         $this->assign('top',$top);
         $this->assign('list',$list);
         return $this->fetch();
     }
    /*
     * 加载更多列表
     */
    public function more(){
        $id = input('post.id');
        $res = VoteModel::where(['status' => 0])->order('id desc')->limit($id,3)->select();
        if ($res){
            foreach($res as $value){
                $Options = VoteOptions::where(['vote_id' => $value->id , 'status' => 0])->select();
                $sum = 0;
                foreach($Options as $val){
                    $sum += $val->num;
                }
                $value['sum'] = $sum;  // 总投票数
                $Pictures = Picture::where(array('id' => $value['front_cover']))->find();
                $value['front_cover'] = $Pictures['path']; // 数据重组  直径获取图片路径在js中显示
                $value['create_time'] = date('Y-m-d H:i',$value['create_time']); // 数据重组  获取时间 年与日
            }
            return $res;
        }else{
            return 0;
        }
    }
    /*
     * 投票 页面
     */
     public function vote(){
         $id = input('id');  // 主题id
         $userId = session('userId');
         $Answer = VoteAnswer::where(['userid' => $userId,'vote_id' => $id])->find();  // 获取投票信息
         $now = time();
         $Vote = VoteModel::where('id',$id)->find();   // 获取截止时间
         $end_time = $Vote['end_time'];
         $dif = $now - $end_time;
         if (empty($Answer) && ($dif < 0)){
            // 未投票  并且 未到截止时间  投票页面 否则 排名页面
             $voteModel = new voteModel();
             // 浏览量 +1
             $info['views'] = array('exp','`views`+1');
             $voteModel::where('id',$id)->update($info);
             if($userId != "visitor"){
                 //浏览不存在则存入sw_browse表
                 $con = array(
                     'user_id' => $userId,
                     'vote_id' => $id, // 投票主题 id
                 );
                 $history = Browse::get($con);
                 if(!$history && $id != 0){
                     Browse::create($con);
                 }
             }
             $list = VoteModel::where('id',$id)->find();
             $list['num'] = VoteOptions::where(['vote_id' => $id,'status' => 0])->count();
             $sum = 0;
             $Options= VoteOptions::where(['vote_id' => $id , 'status' => 0])->select();
             foreach($Options as $value){
                $sum += $value->num;
             }
             $list['sum'] = $sum;
             $this->assign('list',$list);
             $this->assign('link',$_SERVER['HTTP_HOST']);
             return $this->fetch();
         }else{
            //  排名页面
             return $this->fetch('rank',['list' => $Vote]);
         }
     }
    /*
     * 投票 功能
     */
    public function polling(){
        $id = input('id');  // 选项id
        $vid = input('vid');  // 主题id
        $userId = session('userId');
        $map = array(
            'userid' => $userId,
            'vote_id' => $vid
        );
        $data['userid'] = $userId;
        $data['vote_id'] = $vid;
        $data['content'] = $id;
        $res = VoteAnswer::where($map)->find();  // 防止二次投票
        if (empty($res)){
            $Answer = new VoteAnswer();
            $result = $Answer->save($data);
            if ($result){
                // 保存成功后  修改 选项表
                VoteOptions::where('id',$id)->setInc('num');
            }
        }
    }
    /*
     * 投票 排名
     */
     public function rank(){
         $id = input('id');  // 主题id
         $userId = session('userId');
         $Answer = VoteAnswer::where(['userid' => $userId,'vote_id' => $id])->find();  // 获取该用户投票信息
         $list = VoteModel::where('id',$id)->find();
         $list['num'] = VoteOptions::where(['vote_id' => $id,'status' => 0])->count();
         $sum = 0;
         $Options= VoteOptions::where(['vote_id' => $id , 'status' => 0])->select();
         foreach($Options as $value){
             $sum += $value->num;
         }
         $list['sum'] = $sum;  // 投票总人次
         $this->assign('list',$list);  // 投票主题,选项内容
         $this->assign('is',$Answer['content']);
         return $this->fetch();
     }
}