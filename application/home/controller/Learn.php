<?php
/**
 * Created by PhpStorm.
 * User: stiff <1570004138@163.com>
 * Date: 2017/5/11
 * Time: 14:21
 */

namespace app\home\controller;
use app\home\model\WechatUser;
use app\admin\model\Question;
use app\home\model\Answer;
use app\home\model\Study;
use app\home\model\Redfilm;
use app\home\model\Redbook;
use app\home\model\RedbookRead;
use app\home\model\Redmusic;
use app\home\model\Redremark;
class Learn extends Base{
    /**
     * 网上党校主页
     */
    public function index(){

        return $this->fetch();

    }

    /**
     * 两学一做
     */
    public function lesson(){
        $Study = new Study();
        //数据列表
        $map = ['status' => array('egt',0),'recommend' => 1];
        $mapp = ['status' => ['egt',0]];
        $top = $Study->get_list($map,0,true);  // 推荐
        $list = $Study->get_list($mapp);  // 列表
        $this->assign('top',$top);
        $this ->assign('list',$list);
        return $this->fetch();

    }
    /**
     * 主页加载更多
     */
    public function indexmore(){
        $len = input('length');
        $Study = new Study();
        $map = ['status' => array('egt',0)];
        $list = $Study->get_list($map,$len);
        if($list){
            return $this->success("加载成功",'',$list);
        }else{
            return $this->error("加载失败");
        }
    }
    /**
     * 图文详情
     */
    public function article(){
        $this->anonymous();        //判断是否是游客
        $this->jssdk();
        $id = input('id');
        $list = $this->content(3,$id);
        $this->assign('detail',$list);
        return $this->fetch();
    }
    /**
     * 视频课程
     */
    public function video(){
        $this->anonymous();        //判断是否是游客
        $this->jssdk();
        $id = input('id');
        $list = $this->content(3,$id);
        $this->assign('detail',$list);
        return $this->fetch();
    }


    /**
     *  红色珍藏
     */

    public function redcollection(){
        // 红色电影
        $Film = new Redfilm();
        $film = $Film->getIndexList();
        $this->assign('film',$film); //  红色电影
        // 红色文学
        $Book = new Redbook();
        $book = $Book->getIndexList(); // 红色文学
        $this->assign('book',$book);
        // 红色语录
        // 红色音乐
        $Music = new Redmusic();
        $list = $Music->getIndexList();
        $this->assign('music',$list);
        return $this ->fetch();
    }
    /**
     * 经典电影更多
     */
    public function morefilm() {
        $Model = new Redfilm();
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

        }
        return $this->fetch();
    }

    /**
     * 电影详情
     */
    public function filmdetail() {
        $this->anonymous();
        $this->jssdk();
        $id = input('id');
        if (empty($id)){
            $this ->error('参数错误!');
        }
        $detail = $this->content(5,$id);
        $this->assign('detail',$detail);
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
     * 音乐详情
     */
    public function musicdetail() {
        $this->anonymous();
        $this->jssdk();
        $id = input('id');
        if (empty($id)){
            $this ->error('参数错误!');
        }
        $detail = $this->content(7,$id);
        $this->assign('detail',$detail);
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
        }
    }

    /**
     * 书籍详情
     */
    public function bookdetail() {
        $this->anonymous();
        $this->jssdk();
        $id = input('id');
        if (empty($id)){
            $this ->error('参数错误!');
        }
        $detail = $this->content(6,$id);
        $this->assign('detail',$detail);
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
        $id = input('id');
        $res = Redbook::where('id',$id)->setInc('have_read');
        if($res){
            return $this->success("成功读过此书");
        }else{
            return $this->error("新增失败");
        }
    }

   /*
    *  在线答题
   */
    /**
     * 排行榜
     */
    public function rank(){
        $this->anonymous();
        $type = input('type');  //获取类型，1为常规模式排行 2为互动模式排行

        $wechatModel = new WechatUser();
        $userId = session('userId');
        $personal = $wechatModel->where('userid',$userId)->find();    //获取个人信息

        //传统模式
        $map['trad_score'] = array('neq',0);
        $trad = $wechatModel->where($map)->order('trad_score desc')->limit(50)->select();
        foreach ($trad as $key => $value) {
            if($value['userid'] == $userId) {
                $personal['trad_rank'] = $key+1;     //该用户传统排名
            }
        }
        //游戏模式
        $map1['game_score'] = array('neq',0);
        $game = $wechatModel->where($map1)->order('game_score desc')->limit(50)->select();
        foreach ($game as $key => $value) {
            if($value['userid'] == $userId) {
                $personal['game_rank'] = $key+1;     //该用户游戏排名
            }
        }
        if(isset($personal['trad_rank'])) {
            $personal['trad_rank'] = "第".$personal['trad_rank']."名";
        }else {
            $personal['trad_rank'] = "无";
        }
        if(isset($personal['game_rank'])) {
            $personal['game_rank'] = "第".$personal['game_rank']."名";
        }else {
            $personal['game_rank'] = "无";
        }
        $this->assign('per',$personal);
        $this->assign('trad',$trad);
        $this->assign('game',$game);
        $this->assign('type',$type);

        return $this->fetch();
    }

    /**
     * 互动模式主页
     */
    public function game(){
        $this->checkRole();
        //获取该用户的的信息
        $users=$users=session('userId');
        $info = Answer::get(['userid'=>$users]);
        if($info) {
            $exist=$info->exist;
            $this->assign('exist',$exist);
            $this->assign('info',$info);
        }else {
            $this->assign('exist',"");
            $this->assign('info',"");
        }

        return $this->fetch();
    }

    /**
     * 答题页面
     */
    public function answer(){
        $this->checkRole();
        //取单选
        $arr=Question::all(['type'=>0]);
        foreach($arr as $value){
            $ids[]=$value->id;
        }
        //获取用户已经得到的题目
        $users=$users=session('userId');
        $List=Answer::get(['userid'=>$users]);
        if($List !==null){
            $list=$List->question_id;
            $lists=json_decode($list);
        }else{
            $lists=array();
        }
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
        $this->assign('questions',$questions);
        return $this->fetch();
    }
    /*
     * 用户题目保存
     */
    public function save(){
        //获取用户提交信息
        $data=input('post.');
        //将题目ID数目json编码
        $questions=json_encode($data['arrId']);
        //将用户提交答案数组json编码
        $rights=json_encode($data['arr']);
        $users=session('userId');
        //若该用户已经存在则更新
        if(Answer::get(['userid'=>session('userId')])){
            $answer=Answer::get(['userid'=>session('userId')]);
            $answer->question_id=$questions;
            $answer->value=$rights;
            $answer->exist=0;
            if($answer->save()){
                return $this->success('保存成功');
            }else{
                return $this->error('保存失败');
            };
        }
        //若该用户不存在则添加
        $Answer=new Answer();
        $Answer->userid=$users;
        $Answer->question_id=$questions;
        $Answer->value=$rights;
        $Answer->exist=0;

        if($Answer->save()){
            return $this->success('保存成功');
        }else{
            return $this->error('保存失败');
        }
    }
    /*
     * 继续答题
     */
    public function goon(){
        //获取该用户的已经保存的信息
        $users=$users=session('userId');
        $Info=Answer::get(['userid'=>$users]);
        //用户信息不存在,则报错跳转
        if(empty($Info)){
            return $this->error('用户信息不存在错误',Url('Constitution/game'));
        }
        //获取的题目ID,将json格式转化为数组
        $arr=json_decode($Info->question_id,true);
        //获取单选和多选的题目
        foreach($arr as $key=>$value){
            if($key>19){
                $arr2[]=Question::get($value);
            }else{
                $arr1[]=Question::get($value);
            }
        }
        //获取的题目答案,将json格式转化为数组
        $rights=json_decode($Info->value,true);
        //获取单选和多选的答案
        foreach($rights as $key=>$value){
            if($key<20){
                $right1[]=$value;
            }else{
                $right2[]=$value;
            }
        }
        $this->assign('right1',$right1);
        $this->assign('right2',$right2);
        $this->assign('arr1',$arr1);
        $this->assign('arr2',$arr2);
        return $this->fetch();
    }
    /*
     * 用户题目提交
     */
    public function submits(){
        //获取用户提交信息
        $data=input('post.');
        $score=0;
        //判断题目的对错,并改变分数
        foreach($data['arrId'] as $key=>$value){
            $question=Question::get($value);
            if($key <20){
                if($data['arr'][$key]==$question->value){
                    $status[$key]=1;
                    $score++;
                }else{
                    $status[$key]=0;
                }
            }else{
                if($data['arr'][$key]===explode(':',$question->value)){
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
        $users=session('userId');
        //将分数添加至用户积分排名
        $wechatModel = new WechatUser();
        $wechatModel->where('userid',session('userId'))->setInc('game_score',$score);
        //若该用户存在则修改数据
        if(Answer::get(['userid'=>session('userId')])){
            $answer=Answer::get(['userid'=>session('userId')]);
            $answer->question_id=$questions;
            $answer->value=$rights;
            $answer->status=$status;
            $answer->score=$score;
            $answer->exist=1;
            if($answer->save()){
                return $this->success('提交成功');
            }else{
                return $this->error('提交失败');
            };
        }
        //若该用户不存在则添加数据
        $Answer=new Answer();
        $Answer->userid=$users;
        $Answer->question_id=$questions;
        $Answer->value=$rights;
        $Answer->status=$status;
        $Answer->score=$score;
        $Answer->exist=1;
        if($Answer->save()){
            return $this->success('提交成功');
        }else{
            return $this->error('提交失败');
        }

    }
    /*
     * 查看分数
     */
    public function score(){
        $Answer=Answer::get(['userid'=>session('userId')]);
        $WechatUser=WechatUser::get(['userid'=>session('userId')]);
        $num=$WechatUser->game_score;
        $score=$Answer->score;
        $this->assign('num',$num);
        $this->assign('score',$score);
        return $this->fetch();
    }
    /*
     * 查看错题
     */
    public function errors(){
        $Answer=Answer::get(['userid'=>session('userId')]);
        $arr=json_decode($Answer->status,true);
        $lists=json_decode($Answer->question_id,true);
        $rights=json_decode($Answer->value,true);
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
        $this->assign('right2',$right2);
        return $this->fetch();
    }
}