<?php
/**
 * Created by PhpStorm.
 * User: stiff <1570004138@163.com>
 * Date: 2017/5/10
 * Time: 16:08
 */
namespace app\home\controller;
use app\home\model\News;
use app\home\model\Browse;
use  app\home\model\WechatUser;
use app\home\model\Picture;
use app\home\model\Like;
use app\home\model\Comment;
class Dynamic extends Base{
    /**
     * 党委动态
     */
    public function index(){
        //工作部署 type = 1
        $map1 = array(
            'type' => 1,
            'status' => array('egt',0)
        );
        $list1 = News::where($map1)->order('id desc')->limit(5)->select();
        $this->assign('list1',$list1);
        // 中心组学习  轮播
        $maps = array(
            'type' => 2,
            'status' => array('egt',0),
            'recommend' => 1
        );
        $lists = News::where($maps)->order('id desc')->limit(3)->select();
        $this->assign('lists',$lists);
        //中心组 学习  列表 type = 2
        $map2 = array(
            'type' => 2,
            'status' => array('egt',0)
        );
        $list2 = News::where($map2)->order('id desc')->limit(5)->select();
        $this->assign('list2',$list2);
        return $this ->fetch();
    }
    /*
     * 列表  更多
     */
    public function listmore(){
        
    }
    /**
     * 党委动态详情页
     */
    public function detail(){
        //判断是否是游客
        $this->anonymous();
        $userId = session('userId');
        $id = input('id');
        $newsModel = new News();
        //浏览加一
        $info['views'] = array('exp','`views`+1');
        $newsModel::where('id',$id)->update($info);
        if($userId != "visitor"){
            //浏览不存在则存入pb_browse表
            $con = array(
                'user_id' => $userId,
                'news_id' => $id,
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
        $list = $newsModel::get($id);
        $list['user'] = session('userId');
        //分享图片及链接及描述
        $image = Picture::where('id',$list['front_cover'])->find();
        $list['share_image'] = "http://".$_SERVER['SERVER_NAME'].$image['path'];
        $list['link'] = "http://".$_SERVER['SERVER_NAME'].$_SERVER['REDIRECT_URL'];
        $list['desc'] = str_replace('&nbsp;','',strip_tags($list['content']));

        //获取 文章点赞
        $likeModel = new Like;
        $like = $likeModel->getLike(6,$id,$userId);
        $list['is_like'] = $like;
        $this->assign('new',$list);

        //获取 评论
        $commentModel = new Comment();
        $comment = $commentModel->getComment(6,$id,$userId);
        $this->assign('comment',$comment);
        return $this->fetch();
    }
}