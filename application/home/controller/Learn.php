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
    public function lesson(){
//        $this->anonymous();
//        //数据列表
//        $map2 = array(
//            'status' => array('egt',0),
//        );
//        $list2 = LearnModel::where($map2) ->order('id desc') ->limit(5)->select();  // 列表
//        $link = Db::table('pb_paper')->where($map2)->field('content')->find();
//        $this->assign('link',$link['content']);
//        $this ->assign('list2',$list2);
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


    /**
     *  红色珍藏
     */


    public function redcollection(){

        return $this ->fetch();
    }
    /**
     * 红色电影
     */
    public function redfilm() {
   /*     $Model = new Redfilm();
        $list = $Model->getIndexList();
        $this->assign('list',$list);*/
        return $this->fetch();
    }

    /**
     * 经典电影更多
     */
    public function morefilm() {
 /*       $Model = new Redfilm();
        if(IS_POST) {
            //加载更多
            $len = input('length');
            $res = $Model->getMoreList($len);
            if($res) {
                return $this->success("加载成功","",$res);
            }else {
                return $this->error("加载失败");
            }
        }else {
            $list = $Model->getMoreList();
            $this->assign('list',$list);

        }*/
        return $this->fetch();
    }

    /**
     * 电影详情
     */
    public function filmdetail() {
/*        $this->jssdk();
        $id = input('id');
        if (empty($id)){
            $this ->error('参数错误!');
        }
        $detail = $this->content(7,$id);
        $this->assign('detail',$detail);*/
        return $this->fetch();
    }

    /**
     * 电影搜索
     */
    public function filmserch() {
/*        $val = input('val');
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
        }*/
    }

    /**
     * 红色音乐
     */
    public function redmusic() {
      /*  $Model = new Redmusic();
        $list = $Model->getIndexList();
        $this->assign('list',$list);*/
        return $this->fetch();

    }

    /**
     * 音乐详情
     */
    public function musicdetail() {
/*        $this->jssdk();
        $id = input('id');
        if (empty($id)){
            $this ->error('参数错误!');
        }
        $detail = $this->content(8,$id);
        $this->assign('detail',$detail);*/
        return $this->fetch();
    }

    /**
     * 加载更多音乐
     */
    public function moremusic() {
   /*     $len = input('length');
        $musicModel = new Redmusic();
        $map = array(
            'status' => 1,
        );
        $list = $musicModel->where($map)->order('create_time desc')->limit($len,8)->select();
        if($list) {
            foreach ($list as $value) {
                $img = Picture::get($value['front_cover']);
                $value['path'] = $img['path'];
                $value['time'] = date("Y-m-d",$value['create_time']);
            }
            return $this->success("加载成功","",$list);
        }else{
            return $this->error("加载失败");
        }*/
    }

    /**
     * 红色文学
     */
    public function redliterature() {
   /*     $Model = new Redbook();
        if(IS_POST) {
            $len = input('length');
            $res = $Model->getIndexList($len);
            if($res) {
                return $this->success("加载成功","",$res);
            }else {
                return $this->error("加载失败");
            }
        }else {
            $list = $Model->getIndexList();
            $this->assign('list',$list);

        }*/
        return $this->fetch();
    }

    /**
     * 书籍详情
     */
    public function bookdetail() {
        /*$Model = new Redbook();
        $id = input('id');
        if (empty($id)){
            $this ->error('参数错误!');
        }
        $Model->where('id',$id)->setInc('views');
        $detail = $Model->get($id);
        $this->assign('detail',$detail);*/
        return $this->fetch();
    }

    /**
     * 书籍搜索
     */
    public function booksearch() {
/*        $val = input('val');
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
        }*/
    }

    /**
     * 是否读过
     */
    public function is_read() {
      /*  $id = input('id');
        $res = Redbook::where('id',$id)->setInc('have_read');
        if($res){
            return $this->success("成功读过此书");
        }else{
            return $this->error("新增失败");
        }*/
    }

   /*
    *  在线答题
   */


    public function learn(){
        //主页信息
        // 手机党校
       /* $Learn = new LearnModel();
        $map = array(
            'recommend' => 1,
            'status' => ['egt',1]
        );
        $top = $Learn->get_list($map,0,true);
        $info = $Learn->get_list(['status' => ['egt',1]],0);
        $this->assign('top',$top);
        $this->assign('info',$info);
        // 党章
        $list = Constitution::all();
        $this->assign('list',$list);*/
        return $this->fetch();

    }
    // 手机党校 加载更多
    public function more(){
       /* $len = input('post.length');
        $Learn = new LearnModel();
        $list =  $Learn->get_list(['status' => ['egt',1]],$len);
        return $this->success('加载成功','',$list);*/
    }
    /**
     * 答题页面
     */
    public function answer(){
      /*  //取单选
        $arr=Question::all(['type'=>0]);
        foreach($arr as $value){
            $ids[]=$value->id;
        }
        //获取用户已经得到的题目
        $lists=array();
        //随机获取单选的题目
        $num=20;//题目数目
        $data=array();
        while(true){
            if(count($data)==$num){
                break;
            }
            $index=mt_rand(0,count($ids)-1);
            $res=$ids[$index];
            if(!in_array($res,$data) && !in_array($res,$lists)){
                $data[]=$res;
            }
        }
        foreach($data as $value){
            $question[]=Question::get($value);
        }

        //取多选
        $arr2=Question::all(['type'=>1]);
        foreach($arr2 as $value){
            $ids2[]=$value->id;
        }
        //随机获取多选
        $num2=10;//题目数目
        $data2=array();
        while(true){
            if(count($data2)==$num2){
                break;
            }
            $index2=mt_rand(0,count($ids2)-1);
            $res2=$ids2[$index2];
            if(!in_array($res2,$data2) && !in_array($res2,$lists)){
                $data2[]=$res2;
            }
        }
        foreach($data2 as $value){
            $questions[]=Question::get($value);
        }
        $this->assign('question',$question);
        $this->assign('questions',$questions);*/
        return $this->fetch();
    }
    /*
     * 用户题目提交
     */
    public function submits(){
       /* //获取用户提交信息
        $data=input('post.');
        $score=0;;
        //判断题目的对错,并改变分数
        foreach($data['arrId'] as $key=>$value){
            $question=Question::get($value);
            if($key <20){  // 前20单选
                if($data['arr'][$key]==json_decode($question->value)){
                    $status[$key]=1;
                    $score++;
                }else{
                    $status[$key]=0;
                }
            }else{
                if(json_encode($data['arr'][$key])==$question->value){
                    $status[$key]=1;
                    $score++;
                }else{
                    $status[$key]=0;
                }
            }
        }
        //将获取的数据进行json格式转化
        $questions=json_encode($data['arrId']);
        $rights=json_encode($data['arr']);
        $status=json_encode($status);
        $users= md5(uniqid()); //不重复随机id
        //若该用户不存在则添加数据
        $Answer=new Answer();
        $Answer->userid=$users;
        $Answer->question_id=$questions;
        $Answer->value=$rights;
        $Answer->status=$status;
        $Answer->score=$score;
        $Answer->exist=1;
        if($id = $Answer->save()){
            return $this->success('提交成功','',['score' => $score ,'id' => $id]);
        }else{
            return $this->error('提交失败');
        }*/
    }
    /*
     * 查看错题
     */
    public function errors(){
      /*  $id = input('id/d');
        $Answer=Answer::get(['id'=>$id]);
        if (empty($Answer)){
            return $this->error('系统参数错误',Url('Learn/answer'));
        }
        $arr=json_decode($Answer->status,true);
        $lists=json_decode($Answer->question_id,true);
        $rights=json_decode($Answer->value);
        foreach($arr as $key=>$value){
            if($value == 0){
                $Question=Question::get($lists[$key]);
                if($key <20 ){
                    $re[$key]=$Question;
                    $right1[$key]=$rights[$key];
                }else{
                    $res[$key]=$Question;
                    $right2[$key]=$rights[$key];
                }
            }
        }
        $this->assign('question',$re);
        $this->assign('questions',$res);
        $this->assign('right1',$right1);
        $this->assign('right2',$right2);*/
        return $this->fetch();
    }
}