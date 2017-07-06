<?php
/**
 * Created by PhpStorm.
 * User: stiff <1570004138@163.com>
 * Date: 2017/5/11
 * Time: 14:21
 */

namespace app\home\controller;
use app\home\model\WechatUser;
use think\Controller;
use app\admin\model\Question;
use app\home\model\Answers;
use app\home\model\Browse;
use app\home\model\Comment;
use app\admin\model\Picture;
use app\home\model\Learn as LearnModel;
use app\home\model\Like;
use think\Db;

class Learn extends Base{
    /**
     * 两学一做
     */
    public function index(){
        $this->anonymous();
        //数据列表
        $map1 = array(
            'status' => array('egt',0),
            'recommend' => 1
        );
        $map2 = array(
            'status' => array('egt',0),
        );
        $list1 = LearnModel::where($map1)->order('id desc')->limit(3)->select();  // 轮播
        $list2 = LearnModel::where($map2) ->order('id desc') ->limit(2)->select();  // 列表
        $link = Db::table('pb_paper')->where($map2)->field('content')->find();
        $this->assign('link',$link['content']);
        $this->assign('lists',$list1);
        $this ->assign('list2',$list2);
        return $this->fetch();
    }
    /**
     * 主页加载更多
     */
    public function indexmore(){
        $len = input('length');
        $map = array(
            'status' => array('egt',0),
        );
        $list = LearnModel::where($map)->order('id desc')->limit($len,5)->select();
        foreach($list as $value){
            $img = Picture::get($value['front_cover']);
            $value['path'] = $img['path'];
            $value['time'] = date("Y-m-d",$value['create_time']);
        }
        if($list){
            return $this->success("加载成功",'',$list);
        }else{
            return $this->error("加载失败");
        }
    }
    /**
     * 视频课程
     */
    public function video(){
        $this->anonymous();        //判断是否是游客
        $this->jssdk();

        $userId = session('userId');
        $id = input('id');
        $learnModel = new LearnModel();
        //浏览加一
        $info['views'] = array('exp','`views`+1');
        $learnModel::where('id',$id)->update($info);
        if($userId != "visitor"){
            //浏览不存在则存入pb_browse表
            $con = array(
                'user_id' => $userId,
                'learn_id' => $id,
            );
            $history = Browse::get($con);
            if(!$history && $id != 0){
                $s['score'] = array('exp','`score`+1');
                if ($this->score_up()){
                    // 未满 15分
                    Browse::create($con);
                    WechatUser::where('userid',$userId)->update($s);
                }
            }
        }
        $video = $learnModel::get($id);
        $video['user'] = session('userId');
        //分享图片及链接及描述
        $image = Picture::where('id',$video['front_cover'])->find();
        $video['share_image'] = "http://".$_SERVER['SERVER_NAME'].$image['path'];
        $video['link'] = "http://".$_SERVER['SERVER_NAME'].$_SERVER['REDIRECT_URL'];
        $video['desc'] = str_replace('&nbsp;','',strip_tags($video['content']));

        //获取 文章点赞
        $likeModel = new Like;
        $like = $likeModel->getLike(3,$id,$userId);
        $video['is_like'] = $like;
        $this->assign('video',$video);

        //获取 评论
        $commentModel = new Comment();
        $comment = $commentModel->getComment(3,$id,$userId);
        $this->assign('comment',$comment);
        return $this->fetch();
    }
}