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
use app\home\model\WechatUser;

/*
 * 选举投票主页
*/

class Vote extends Base{
     /*
      * 投票主页
      */
     public function index(){
         $this->anonymous();
         $userId = session('userId');
         //  获取该用户 所在支部
         $Depart = WechatUser::where('userid',$userId)->field('department')->find();
         $depart = json_decode($Depart['department']);
         $this->jssdk();
         $map = array(
             'status' => array('egt',0),
             'end_time' => array('gt',time()),  // 未结束
             'publisher' => array('in',$depart)
         );
         $maps = array(
             'status' => array('egt',0),
             'end_time' => array('elt',time()),  // 历史投票
             'publisher' => array('in',$depart)
         );
         $voteModel = new VoteModel();
         $top = $voteModel->where($map)->order('id desc')->select(); // 未结束
         foreach($top as $value){
             $secs = $value['end_time'] - time();
             $result = '';
             if ($secs >= 86400) {
                 $days = floor($secs / 86400);
                 $secs = $secs % 86400;
                 $result = $days . ' 天';
                 if ($secs > 0) {
                     $result .= ' ';
                 }
             }
             if ($secs >= 3600) {
                 $hours = floor($secs / 3600);
                 $secs = $secs % 3600;
                 $result .= $hours . ' 小时';
                 if ($secs > 0) {
                     $result .= ' ';
                 }
             }
             if ($secs >= 60) {
                 $minutes = floor($secs / 60);
                 $secs = $secs % 60;
                 $result .= $minutes . ' 分钟';
                 if($secs > 0) {
                     $result .= ' ';
                 }
             }
             $value['left'] = $result;
             $Options = VoteOptions::where(['vote_id' => $value->id , 'status' => 0])->select();
             $sum = 0;
             foreach($Options as $val){
                 $sum += $val->num;
             }
             $value['sum'] = $sum;
         }
         $list = $voteModel->where($maps)->order('id desc')->select();  // 历史记录
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
        $this->checkRole();
        $id = input('post.id');
        $res = VoteModel::where(['status' => ['egt',0]])->order('id desc')->limit($id,3)->select();
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
         $this->checkRole();
         $id = input('get.id');  // 主题id
         $type = input('get.type');  // 类型
         $userId = session('userId');
         if ($type == 1){
             // 进行中
             $Answer = VoteAnswer::where(['userid' => $userId,'vote_id' => $id])->find();  // 获取投票信息
             if (empty($Answer)){
                 // 未投票 
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
                 //  已经投票  排名页面
                 $Vote = VoteModel::where('id',$id)->find();   // 获取 投票信息
                 $Vote['num'] = VoteOptions::where(['vote_id' => $id,'status' => 0])->count();  // 候选人
                 $sum = 0;
                 $Options= VoteOptions::where(['vote_id' => $id , 'status' => 0])->select();
                 foreach($Options as $value){
                     $sum += $value->num;
                 }
                 $Vote['sum'] = $sum;  // 投票人次
                 $data = VoteAnswer::where(['vote_id' => $id,'userid' => $userId])->find();
                 $Vote['vote'] = $data['content'];
                 return $this->fetch('rank',['list' => $Vote]);
             }
         }else{
             // 已结束
             $Vote = VoteModel::where('id',$id)->find();   // 获取 投票信息
             $Vote['num'] = VoteOptions::where(['vote_id' => $id,'status' => 0])->count();  // 候选人
             $sum = 0;
             $Options= VoteOptions::where(['vote_id' => $id , 'status' => 0])->select();
             foreach($Options as $value){
                 $sum += $value->num;
             }
             $Vote['sum'] = $sum;  // 投票人次
             $data = VoteAnswer::where(['vote_id' => $id,'userid' => $userId])->find();
             $Vote['vote'] = $data['content'];
             return $this->fetch('rank',['list' => $Vote]);
         }
         
     }
    /*
     * 投票 功能
     */
    public function polling(){
        $this->checkRole();
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
         $this->checkRole();
         $id = input('id');  // 主题id
         $userId = session('userId');
         $list = VoteModel::where('id',$id)->find();
         $list['num'] = VoteOptions::where(['vote_id' => $id,'status' => 0])->count();
         $sum = 0;
         $Options= VoteOptions::where(['vote_id' => $id , 'status' => 0])->select();
         foreach($Options as $value){
             $sum += $value->num;
         }
         $list['sum'] = $sum;  // 投票总人次
         $data = VoteAnswer::where(['vote_id' => $id,'userid' => $userId])->find();
         $list['vote'] = $data['content'];
         $this->assign('list',$list);  // 投票主题,选项内容
         return $this->fetch();
     }
    /*
     * 视频会议
     */
    public function meet(){
        $this->checkRole();
        return $this->fetch();
    }
}