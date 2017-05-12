<?php
/**
 * Created by PhpStorm.
 * User: Lxx<779219930@qq.com>
 * Date: 2016/11/21
 * Time: 10:27
 */

namespace app\home\controller;
use app\home\model\Comment;
use app\home\model\Like;
use app\home\model\Opinion;
use app\home\model\Picture;
use com\wechat\TPQYWechat;
use think\Config;
use think\Db;

/**
 * Class Feedback
 * @package app\home\controller
 * 党员之声
 */
class Feedback extends Base {
    /**
     * 主页
     */
    public function index() {
        $userId = session('userId');
        $map = array(
            'status' => array('eq',1),
        );
        $optionModel = new Opinion();
        $list = $optionModel->where($map)->order('id desc')->limit(7)->select();
        foreach ($list as $value) {
            //获取用户信息
            $value['images'] = json_decode($value['images']);
            $value['username'] = $value->user->name;
            ($value->user->header) ? $value['header'] = $value->user->header : $value['header'] = $value->user->avatar;
            //获取相关意见反馈评论
            $map1 = array(
                'opinion_id' => $value['id'],
                'type' => 4,
                'status' => array('egt',0)
            );
            $comment = Comment::where($map1)->select();
            foreach ($comment as $k => $val){
                $val['username'] = $val->user->name;
            }
            $value['comment'] = $comment;

            //是否点赞
            $map2 = array(
                'opinion_id' => $value['id'],
                'create_user' => $userId,
                'status' => 0,
                'type' => 1,
            );
            $msg = Like::where($map2)->find();
            if($msg) {
                $value['is_like'] = 1;
            }else{
                $value['is_like'] = 0;
            }
        }
        $this->assign('list',$list);

        return $this->fetch();
    }

    /**
     * 反馈提交页
     */
    public function publish() {
        if(IS_POST) {
            $userId = session('userId');
            $data = input('post.');
            $department = Db::table('pb_wechat_department_user')
                ->alias('a')
                ->join('pb_wechat_department b','a.departmentid = b.id','LEFT')
                ->where('a.userid',$userId)
                ->find();
            $data['department_name'] = $department['name'];
            $data['images'] = json_encode($data['images']);
            $data['create_user'] = $userId;
            $opinionModel = new Opinion();
            $model = $opinionModel->create($data);
            if($model) {
                $map['status'] = 0;
//                $count = $opinionModel->where($map)->count();
//                $content = "您有".$count."条[意见反馈]审核消息，请点击【反馈审核】及时查看。";
//                $Wechat = new TPQYWechat(Config::get('party'));
//                $message = array(
//                    "totag" => 3,
//                    "msgtype" => 'text',
//                    "agentid" => 13,
//                    "text" => array(
//                        "content" => $content
//                    ),
//                    "safe" => "0"
//                );
//                $Wechat->sendMessage($message);  //审核通过，向用户推送提示
                return $this->success("提交成功");
            }else{
                return $this->error("提交失败");
            }
        }else{
            return $this->fetch();
        }
    }

    /**
     * 加载更多
     */
    public function more(){
        $len = input('length');
        $map = array(
            'status' => array('eq',1)
        );
        $list = Opinion::where($map)->order('id desc')->limit($len,7)->select();
        foreach($list as $value){
            //获取用户信息
            $value['images'] = json_decode($value['images']);
            $image =array();
            foreach ($value['images'] as $k=>$val){
                $img = Picture::get($val);
                $image[$k] = $img['path'];
            }
            $value['images'] = $image;
            $value['username'] = $value->user->name;
            ($value->user->header) ? $value['header'] = $value->user->header : $value['header'] = $value->user->avatar;
            //获取相关意见反馈评论
            $map1 = array(
                'opinion_id' => $value['id'],
                'type' => 4,
                'status' => array('egt',0)
            );
            $comment = Comment::where($map1)->select();
            foreach ($comment as $k => $val){
                $val['username'] = $val->user->name;
            }
            $value['comment'] = $comment;

            //是否点赞
            $map2 = array(
                'opinion_id' => $value['id'],
                'create_user' => session('userId'),
                'status' => 0,
                'type' => 1,
            );
            $msg = Like::where($map2)->find();
            if($msg) {
                $value['is_like'] = 1;
            }else{
                $value['is_like'] = 0;
            }
            $value['time'] = date("Y.m.d",$value['create_time']);
        }
        if($list){
            return $this->success("加载成功","",$list);
        }else{
            return $this->error("加载失败");
        }
    }
}