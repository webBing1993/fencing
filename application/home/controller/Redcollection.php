<?php
/**
 * Created by PhpStorm.
 * User: Lxx<779219930@qq.com>
 * Date: 2017/1/3
 * Time: 14:00
 */

namespace app\home\controller;
use app\home\model\Comment;
use app\home\model\Like;
use app\home\model\Picture;
use app\home\model\Redbook;
use app\home\model\RedbookRead;
use app\home\model\Redfilm;
use app\home\model\Redmusic;
use app\home\model\Redremark;
use app\home\model\WechatUser;
use app\home\model\Browse;

/**
 * Class Redcollection
 * @package app\home\controller
 * 红色珍藏
 */
class Redcollection extends Base {
    /**
     * 红色电影
     */
    public function redfilm() {
        $filmModel = new Redfilm();
        $map = array(
            'status' => 1,
        );
        //最新上映
        $new = $filmModel->where($map)->order('create_time desc')->limit(6)->select();
        $this->assign('new',$new);

        //经典热播
        $classic = $filmModel->where($map)->order('views desc,create_time desc')->limit(6)->select();
        $this->assign('classic',$classic);

        //轮播
        $map['recommend'] = 1;
        $carousel = $filmModel->where($map)->order('create_time desc')->limit(3)->select();
        $this->assign('carousel',$carousel);
        return $this->fetch();
    }

    /**
     * 经典电影更多
     */
    public function morefilm() {
        $filmModel = new Redfilm();
        $map = array(
            'status' => 1,
        );
        $list = $filmModel->where($map)->order('views desc,create_time desc')->select();
        $this->assign('list',$list);
        return $this->fetch();
    }

    /**
     * 电影详情
     */
    public function filmdetail() {
        $this->anonymous(); //判断是否是游客
        $id = input('id');
        $filmModel = new Redfilm();
        $userId = session('userId');
        //浏览加一
        $info['views'] = array('exp','`views`+1');
        $filmModel::where('id',$id)->update($info);
        if($userId != "visitor"){
            //浏览不存在则存入pb_browse表
            $con = array(
                'user_id' => $userId,
                'film_id' => $id,
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
        $film = $filmModel->get($id);

        //获取 文章点赞
        $likeModel = new Like;
        $like = $likeModel->getLike(3,$id,$userId);
        $film['is_like'] = $like;

        $this->assign('film',$film);
        //获取 评论
        $commentModel = new Comment();
        $comment = $commentModel->getComment(3,$id,$userId);
        $this->assign('comment',$comment);
        return $this->fetch();
    }

    /**
     * 电影搜索
     */
    public function filmserch() {
        $val = input('val');
        if($val) {
            $map = array(
                'title' => array('like','%'.$val.'%'),
                'status' => 1,
            );
            $filmModel = new Redfilm();
            $list = $filmModel->where($map)->order('create_time desc')->column('id,title');
            if($list) {
                return $this->success("查询成功","",$list);
            }else{
                return $this->error("未查询到数据");
            }
        }else {
            return $this->error("查询条件不能为空");
        }
    }

    /**
     * 红色音乐
     */
    public function redmusic() {
        $musicModel = new Redmusic();
        $map = array(
            'status' => 1,
        );
        $list = $musicModel->where($map)->order('create_time desc')->limit(8)->select();
        $this->assign('list',$list);

        return $this->fetch();
    }

    /**
     * 音乐详情
     */
    public function musicdetail() {
        $this->anonymous(); //判断是否是游客
        $id = input('id');
        $userId = session('userId');
        $musicModel = new Redmusic();
        //浏览加一
        $info['views'] = array('exp','`views`+1');
        $musicModel::where('id',$id)->update($info);
        if($userId != "visitor"){
            //浏览不存在则存入pb_browse表
            $con = array(
                'user_id' => $userId,
                'music_id' => $id,
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

        $music = $musicModel->get($id);
        //获取 文章点赞
        $likeModel = new Like;
        $like = $likeModel->getLike(4,$id,$userId);
        $music['is_like'] = $like;
        $this->assign('music',$music);

        //获取 评论
        $commentModel = new Comment();
        $comment = $commentModel->getComment(4,$id,$userId);
        $this->assign('comment',$comment);
        return $this->fetch();
    }

    /**
     * 加载更多音乐
     */
    public function moremusic() {
        $len = input('length');
        $musicModel = new Redmusic();
        $map = array(
            'status' => 1,
        );
        $list = $musicModel->where($map)->order('create_time desc')->limit($len,5)->select();
        if($list) {
            foreach ($list as $value) {
                $img = Picture::get($value['front_cover']);
                $value['path'] = $img['path'];
                $value['time'] = date("Y-m-d",$value['create_time']);
            }
            return $this->success("加载成功","",$list);
        }else{
            return $this->error("加载失败");
        }
    }

    /**
     * 红色文学
     */
    public function redliterature() {
        $map = array(
            'status' => 1,
        );
        //红色书籍
        $bookModel = new Redbook();
        $book = $bookModel->where($map)->order('create_time desc,have_read desc')->select();
        $this->assign('books',$book);

        //经典语录
        $remarkModel = new Redremark();
        $remark = $remarkModel->where($map)->order('create_time desc')->select();
        $this->assign('remark',$remark);
        return $this->fetch();
    }

    /**
     * 书籍详情
     */
    public function bookdetail() {
        $this->anonymous(); //判断是否是游客
        $id = input('id');
        $userId = session('userId');
        $bookModel = new Redbook();
        //浏览加一
        $info['views'] = array('exp','`views`+1');
        $bookModel::where('id',$id)->update($info);
        if($userId != "visitor"){
            //浏览不存在则存入pb_browse表
            $con = array(
                'user_id' => $userId,
                'book_id' => $id,
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
        $book = $bookModel->get($id);   //基础信息

        //是否读过此书
        $userId = session('userId');
        $info1 = array(
            'create_user' => $userId,
            'book_id' => $id,
        );
        $isread =  RedbookRead::where($info1)->find();
        if($isread) {
            $book['is_read'] = 1; //已读过
        }else{
            $book['is_read'] = 0;
        }
        
        //获取 文章点赞
        $likeModel = new Like;
        $like = $likeModel->getLike(5,$id,$userId);
        $book['is_like'] = $like;
        $this->assign('book',$book);

        //获取 评论
        $commentModel = new Comment();
        $comment = $commentModel->getComment(5,$id,$userId);
        $this->assign('comment',$comment);
        
        return $this->fetch();
    }

    /**
     * 书籍搜索
     */
    public function booksearch() {
        $val = input('val');
        if($val) {
            $map = array(
                'title' => array('like','%'.$val.'%'),
                'status' => 1,
            );
            $map2 = array(
                'name' => array('like','%'.$val.'%'),
            );
            $bookModel = new Redbook();
            $list = $bookModel->where($map)->whereOr($map2)->order('create_time desc')->column('id,title');
            if($list) {
                return $this->success("查询成功","",$list);
            }else{
                return $this->error("未查询到数据");
            }
        }else {
            return $this->error("查询条件不能为空");
        }
    }

    /**
     * 是否读过
     */
    public function is_read() {
        $userId = session('userId');
        $id = input('id');
        $readModel = new RedbookRead();
        $data = array(
            'create_user' => $userId,
            'book_id' => $id,
        );
        $info = $readModel->where($data)->find();
        if(empty($info)) {
            $model = $readModel->create($data);
            if($model) {
                $map['have_read'] = array('exp','`have_read`+1');
                Redbook::where('id',$id)->update($map);
                return $this->success("成功读过此书");
            }else{
                return $this->error("新增失败");
            }
        }else{
            return $this->error("该数据已存在");
        }
    }

    /**
     * 语录详情
     */
    public function quotationdetail() {
        $id = input('id');
        $remarkModel = new Redremark();
        $remark = $remarkModel->get($id);
        $remark['content'] = json_decode($remark['content'],true);
        $this->assign('remark',$remark);
        return $this->fetch();
    }

}