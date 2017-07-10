<?php
/**
 * Created by PhpStorm.
 * User: Lxx<779219930@qq.com>
 * Date: 2016/9/12
 * Time: 15:56
 */

namespace app\home\controller;
use app\home\model\Browse;
use app\home\model\Collect;
use app\home\model\Comment;
use app\home\model\Learn;
use app\home\model\Like;
use app\home\model\Notice;
use app\home\model\Opinion;
use app\home\model\Picture;
use app\home\model\Redbook;
use app\home\model\Redfilm;
use app\home\model\Redmusic;
use app\home\model\WechatUser;
use think\Controller;
use app\home\model\News as NewsModel;
/**
 * Class News
 * @package 党建动态
 */
class News extends Base {
    /**
     * 主页
     */
    public function index(){
        //列表
        $map1 = array(
            'type' => 1 , // 新闻聚焦
            'status' => array('egt',0),
        );
        $list1 = NewsModel::where($map1)->order('id desc')->limit(6)->select();
        $this->assign('list1',$list1);
        $map2 = array(
            'type' => 2 , // 各地动态
            'status' => array('egt',0),
        );
        $list2 = NewsModel::where($map2)->order('id desc')->limit(6)->select();
        $this->assign('list2',$list2);
        $map3 = array(
            'type' => 3 , // 意见文件
            'status' => array('egt',0),
        );
        $list3 = NewsModel::where($map3)->order('id desc')->limit(6)->select();
        $this->assign('list3',$list3);
        return $this->fetch();
    }

    /**
     * 新闻内容页
     */
    public function detail(){
        $this->anonymous();
        $this->jssdk();
        
        $id = input('id');
        $userId = session('userId');
        //浏览加一
        $info['views'] = array('exp','`views`+1');
        NewsModel::where('id',$id)->update($info);

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
                    // 未超过 15分
                    WechatUser::where('userid',$userId)->update($s);
                    Browse::create($con);
                }
            }
        }
        //详细信息
        $info = NewsModel::get($id);
        //分享图片及链接及描述
        $image = Picture::where('id',$info['front_cover'])->find();
        $info['share_image'] = "http://".$_SERVER['SERVER_NAME'].$image['path'];
        $info['link'] = "http://".$_SERVER['SERVER_NAME'].$_SERVER['REDIRECT_URL'];
        $info['desc'] = str_replace('&nbsp;','',strip_tags($info['content']));

        //获取 文章点赞
        $likeModel = new Like;
        $like = $likeModel->getLike(1,$id,$userId);
        $info['is_like'] = $like;
        $this->assign('new',$info);

        //获取 评论
        $commentModel = new Comment();
        $comment = $commentModel->getComment(1,$id,$userId);
        $this->assign('comment',$comment);
        return $this->fetch();
    }

    /**
     * 列表加载更多
     */
    public function listmore(){
        $len = input('length');
        $type = input('type');
        $map = array(
            'type' => $type,
            'status' => array('egt',0),
        );
        $list = NewsModel::where($map)->order('id desc')->limit($len,5)->select();
        foreach($list as $value){
            $img = Picture::get($value['front_cover']);
            $value['src'] = $img['path'];
            $value['time'] = date("Y-m-d",$value['create_time']);
        }
        if($list){
            return $this->success("加载成功",'',$list);
        }else{
            return $this->error("加载失败");
        }
    }

}