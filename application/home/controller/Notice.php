<?php
/**
 * Created by PhpStorm.
 * User: Lxx<779219930@qq.com>
 * Date: 2016/9/12
 * Time: 16:12
 */

namespace app\home\controller;
use app\home\model\Browse;
use app\home\model\Comment;
use app\home\model\Like;
use app\home\model\Picture;
use app\home\model\WechatUser;
use app\home\model\WechatUserTag;
use com\wechat\TPQYWechat;
use think\Config;
use think\Controller;

use app\home\model\Notice as NoticeModel;
use think\Db;

/**
 * Class Notice
 * @package 支部动态
 */
class Notice extends Base {
    /**
     * 主页
     */
    public function index(){
        $this->anonymous(); //判断是否是游客
        $this->default_pic();
        //相关通知 type = 1
        $map1 = array(
            'type' => 1,
            'status' => array('egt',0)
        );
        $list1 = NoticeModel::where($map1)->order('id desc')->limit(2)->select();
        $this->assign('relevant',$list1);
        //情况报道  type = 2
        $map2 = array(
            'type' => 2,
            'status' => array('egt',0)
        );
        $list2 = NoticeModel::where($map2)->order('id desc')->limit(5)->select();
        $this->assign('meet',$list2);

        //活动通知  type = 3
        $map4 = array(
            'type' => 3,
            'status' => array('egt',0)
        );
        $list4 = NoticeModel::where($map4)->order('id desc')->limit(2)->select();
        $this->assign('recruit',$list4);

        //活动情况 type = 4
        $map5 = array(
            'type' => 4,
            'status' => array('egt',0)
        );
        $list5 = NoticeModel::where($map5)->order('id desc')->limit(5)->select();
        $this->assign('activity',$list5);
        return $this->fetch();
    }

    /**
     * 相关通知  活动通知  列表
     */
    public function relevantlist(){
        $type = input('get.type/d');
        if ($type == 1){
            // 相关通知
            $map = array(
                'status' => array('egt',0),
                'type' => 1,
            );
        }else{
            // 活动通知
            $map = array(
                'status' => array('egt',0),
                'type' => 3,
            );
        }
        $noticeModel = new NoticeModel();
        $list = $noticeModel::where($map)->order('id desc')->limit(7)->select();
        //判断是否为空
        if (empty($list)){
            $this->assign('show',0);
        }else{
            $this->assign('show',1);
        }
        foreach ($list as $value) {
            if($value['end_time'] < time()) {
                $value['is'] = 1;
            }else{
                $value['is'] = 0;
            }
        }
        $this->assign('type',$type);
        $this->assign('list',$list);
        return $this->fetch();
    }

    /**
     * 更多  通知
     */
    public function relevantmore(){
        $len = input('length');
        $type = input('type');
        if ($type == 1){
            // 相关通知
            $map = array(
                'type' => 1,
                'status' => array('egt',0),
            );
        }else{
            // 活动通知
            $map = array(
                'type' => 3,
                'status' => array('egt',0),
            );
        }
        $list = NoticeModel::where($map)->order('id desc')->limit($len,7)->select();
        foreach($list as $value){
            $value['time'] = date("Y-m-d",$value['create_time']);
            if($value['end_time'] < time()) {
                $value['state'] = 1;
            }else{
                $value['state'] = 0;
            }
        }
        if($list){
            return $this->success("加载成功",'',$list);
        }else{
            return $this->error("加载失败");
        }
    }
    /**
     *  相关通知  活动通知 详细页
     */
    public function recruit(){

        //判断是否是游客
        $this->anonymous();
        $this->jssdk();
        $userId = session('userId');
        $id = input('id');
        $noticeModel = new NoticeModel();
        //浏览加一
        $info['views'] = array('exp','`views`+1');
        $noticeModel::where('id',$id)->update($info);
        if($userId != "visitor"){
            //浏览不存在则存入pb_browse表
            $con = array(
                'user_id' => $userId,
                'notice_id' => $id,
            );
            $history = Browse::get($con);
            if(!$history && $id != 0){
                $s['score'] = array('exp','`score`+1');
                if ($this->score_up()){
                    // 未满 15 分
                    Browse::create($con);
                    WechatUser::where('userid',$userId)->update($s);
                }
            }
        }

        //活动基本信息
        $list = $noticeModel::get($id);
        $list['user'] = session('userId');
        //分享图片及链接及描述
        $image = Picture::where('id',$list['front_cover'])->find();
        $list['share_image'] = "http://".$_SERVER['SERVER_NAME'].$image['path'];
        $list['link'] = "http://".$_SERVER['SERVER_NAME'].$_SERVER['REDIRECT_URL'];
        $list['desc'] = str_replace('&nbsp;','',strip_tags($list['content']));

        //获取 文章点赞
        $likeModel = new Like;
        $like = $likeModel->getLike(2,$id,$userId);
        $list['is_like'] = $like;
        $this->assign('list',$list);

        //获取 评论
        $commentModel = new Comment();
        $comment = $commentModel->getComment(2,$id,$userId);
        $this->assign('comment',$comment);
        return $this->fetch();
    }

    /**
     *  情况报道 活动情况
     */
    public function activity(){
        //判断是否是游客
        $this->anonymous();
        $this->jssdk();

        $userId = session('userId');
        $id = input('id');
        $noticeModel = new NoticeModel();
        //浏览加一
        $info['views'] = array('exp','`views`+1');
        $noticeModel::where('id',$id)->update($info);
        if($userId != "visitor"){
            //浏览不存在则存入pb_browse表
            $con = array(
                'user_id' => $userId,
                'notice_id' => $id,
            );
            $history = Browse::get($con);
            if(!$history && $id != 0){
                $s['score'] = array('exp','`score`+1');
                if ($this->score_up()){
                    // 未满 15 分
                    Browse::create($con);
                    WechatUser::where('userid',$userId)->update($s);
                }
            }
        }

        $activity = $noticeModel->get($id);
        //重组轮播图片
        $activity['user'] = session('userId');
        //分享图片及链接及描述
        $image = Picture::where('id',$activity['front_cover'])->find();
        $activity['share_image'] = "http://".$_SERVER['SERVER_NAME'].$image['path'];
        $activity['link'] = "http://".$_SERVER['SERVER_NAME'].$_SERVER['REDIRECT_URL'];
        $activity['desc'] = str_replace('&nbsp;','',strip_tags($activity['content']));

        //获取 文章点赞
        $likeModel = new Like;
        $like = $likeModel->getLike(2,$id,$userId);
        $activity['is_like'] = $like;
        $activity['images'] = json_decode($activity['images']);
        $this->assign('activity',$activity);

        //获取 评论
        $commentModel = new Comment();
        $comment = $commentModel->getComment(2,$id,$userId);
        $this->assign('comment',$comment);

        return $this->fetch();
    }
    
    /**
     * 更多活动详情
     */
    public function activitymore(){
        $len = input("length");
        $type = input('type/d');
        if ($type == 1){
            // 情况报道
            $map = array(
                'type' => 2,  // 情况报道
                'status' => array('egt',0)
            );
        }else{
            // 活动情况
            $map = array(
                'type' => 4,
                'status' => array('egt',0)
            );
        }
        $list = NoticeModel::where($map)->order('id desc')->limit($len,7)->select();
        foreach($list as $value){
            $img = Picture::get($value['front_cover']);
            $value['path'] = $img['path'];
            $value['time'] = date("Y-m-d",$value['create_time']);
        }
        if($list){
            return $this->success("加载成功","",$list);
        }else{
            return $this->error("加载失败");
        }
    }
}