<?php
namespace app\home\controller;
/**
 * Created by PhpStorm.
 * User: 老王
 * Date: 2017/7/18
 * Time: 9:19
 */
use app\home\model\Special as SpecialModel;
use app\home\model\Browse;
use app\home\model\WechatUser;
use app\home\model\Picture;
use app\home\model\Like;
use app\home\model\Comment;
use think\Db;
// 专题 模块
class Special extends Base
{
  /*
   * 专题模块 列表
   */
    public function index(){
        // 主题
        $map = array(
            'type' => 1,
            'status' => ['egt',0]
        );
        $topic = SpecialModel::where($map)->find();
        $this->assign('topic',$topic);
        // 专题列表
        $maps = array(
            'type' => 2,
            'status' => ['egt',0]
        );
        $list = SpecialModel::where($maps)->order('id desc')->limit(7)->select();
        $this->assign('list',$list);
        return $this->fetch();
    }
    /*
     * 专题 详情
     */
    public function detail(){
        $this->anonymous();
        $this->jssdk();

        $userId = session('userId');
        if (IS_POST){
            $post_id = input('post.insert_id');
            Db::name('stay_time')->where('id',$post_id)->update(['end_time' => time()]);
        }else{
            $id = input('id');
            Db::name('stay_time')->insert(['userid' => $userId,'type' => 2,'aid' =>$id, 'start_time' => time(),'end_time' => time()+5]);
            $insert_id = Db::name('stay_time')->getLastInsID();
            //浏览加一
            $info['views'] = array('exp','`views`+1');
            SpecialModel::where('id',$id)->update($info);

            if($userId != "visitor"){
                //浏览不存在则存入pb_browse表
                $con = array(
                    'user_id' => $userId,
                    'special_id' => $id,
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
            $info = SpecialModel::get($id);
            //分享图片及链接及描述
            $image = Picture::where('id',$info['front_cover'])->find();
            $info['share_image'] = "http://".$_SERVER['SERVER_NAME'].$image['path'];
            $info['link'] = "http://".$_SERVER['SERVER_NAME'].$_SERVER['REDIRECT_URL'];
            $info['desc'] = str_replace('&nbsp;','',strip_tags($info['content']));

            //获取 文章点赞
            $likeModel = new Like;
            $like = $likeModel->getLike(6,$id,$userId);
            $info['is_like'] = $like;
            $this->assign('new',$info);

            //获取 评论
            $commentModel = new Comment();
            $comment = $commentModel->getComment(6,$id,$userId);
            $this->assign('comment',$comment);
            $this->assign('insert_id',$insert_id);
            return $this->fetch();
        }
    }
    /*
     * 专题 列表 更多
     */
    public function more(){
        $len = input('length');
        $type = input('type');
        $map = array(
            'type' => $type,
            'status' => array('egt',0),
        );
        $list = SpecialModel::where($map)->order('id desc')->limit($len,5)->select();
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