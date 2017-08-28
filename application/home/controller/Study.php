<?php
/**
 * Created by PhpStorm.
 * User: Lxx<779219930@qq.com>
 * Date: 2016/9/12
 * Time: 16:34
 */

namespace app\home\controller;
use app\admin\model\Picture;
use app\home\model\Browse;
use app\home\model\Comment;
use app\home\model\Like;
use app\home\model\WechatUser;
use think\Controller;
use app\home\model\Study as StudyModel;
use think\Db;

/**
 *
 * @package 两学一做
 */
class Study extends Base {
    /**
     * 主页
     */
    public function index(){
        //推荐
        $map1 = array(
            'type' => array('in',[1,2,3]),
            'status' => 0,
            'recommend' => 1
        );
        $list1 = StudyModel::where($map1)->limit(3)->order('id desc')->select();
        $this->assign('list1',$list1);

        //数据列表
        $map2 = array(
            'type' => array('in',[1,2,3]),
            'status' => array('egt',0),
        );
        $list2 = StudyModel::where($map2)->limit(5)->order('id desc')->select();
        $this->assign('list2',$list2);

        return $this->fetch();
    }

    /**
     * 主页加载更多
     */
    public function indexmore(){
        $len = input('length');
        $map = array(
            'type' => array('in',[1,2,3]),
            'status' => array('eq',0),
        );
        $list = StudyModel::where($map)->order('id desc')->limit($len,5)->select();
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
        $userId = session('userId');
        if (IS_POST){
            $post_id = input('post.insert_id');
            Db::name('stay_time')->where('id',$post_id)->update(['end_time' => time()]);
        }else{
            $id = input('id');
            Db::name('stay_time')->insert(['userid' => $userId,'type' => 1,'aid' =>$id, 'start_time' => time(),'end_time' => time()+5]);
            $insert_id = Db::name('stay_time')->getLastInsID();
            $this->anonymous();        //判断是否是游客
            $this->jssdk();
            $learnModel = new StudyModel();
            //浏览加一
            $info['views'] = array('exp','`views`+1');
            $learnModel::where('id',$id)->update($info);
            if($userId != "visitor"){
                //浏览不存在则存入pb_browse表
                $con = array(
                    'user_id' => $userId,
                    'study_id' => $id,
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
            $like = $likeModel->getLike(8,$id,$userId);
            $video['is_like'] = $like;
            $this->assign('video',$video);

            //获取 评论
            $commentModel = new Comment();
            $comment = $commentModel->getComment(8,$id,$userId);
            $this->assign('comment',$comment);
            $this->assign('insert_id',$insert_id);
            return $this->fetch();
        }
    }

    /**
     * 图文课程
     */
    public function article(){
        $this->anonymous();        //判断是否是游客
        $this->jssdk();

        $userId = session('userId');
        if (IS_POST){
            $post_id = input('post.insert_id');
            Db::name('stay_time')->where('id',$post_id)->update(['end_time' => time()]);
        }else{
            $id = input('id');
             Db::name('stay_time')->insert(['userid' => $userId,'type' => 1,'aid' =>$id, 'start_time' => time(),'end_time' => time()+5]);
            $insert_id = Db::name('stay_time')->getLastInsID();
            $learnModel = new StudyModel();
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
                        // 未满 15 分
                        Browse::create($con);
                        WechatUser::where('userid',$userId)->update($s);
                    }
                }
            }
            $article = $learnModel::get($id);
            $article['user'] = session('userId');
            //分享图片及链接及描述
            $image = Picture::where('id',$article['front_cover'])->find();
            $article['share_image'] = "http://".$_SERVER['SERVER_NAME'].$image['path'];
            $article['link'] = "http://".$_SERVER['SERVER_NAME'].$_SERVER['REDIRECT_URL'];
            $article['desc'] = str_replace('&nbsp;','',strip_tags($article['content']));

            //获取 文章点赞
            $likeModel = new Like;
            $like = $likeModel->getLike(8,$id,$userId);
            $article['is_like'] = $like;
            $this->assign('article',$article);

            //获取 评论
            $commentModel = new Comment();
            $comment = $commentModel->getComment(8,$id,$userId);
            $this->assign('comment',$comment);
            $this->assign('insert_id',$insert_id);
            return $this->fetch();
        }
    }
    
    
}