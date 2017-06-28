<?php
/**
 * Created by PhpStorm.
 * User: Lxx<779219930@qq.com>
 * Date: 2016/10/19
 * Time: 19:09
 */

namespace app\home\controller;
use app\home\model\Browse;
use app\home\model\Comment;
use app\home\model\Like;
use app\home\model\Opinion;
use app\home\model\Picture;
use  app\admin\model\WechatDepartment;
use app\home\model\Company as CompanyModel;
use app\home\model\WechatDepartmentUser;
use app\home\model\WechatUser;
use think\Request;
use think\Db;
/*
 * 党员之家
*/

class Company extends Base{
    /*
     * 交流互动   主页
     */
    public function index(){
        $this->anonymous();
        $this->jssdk();
        $Model = new CompanyModel();
        $order = array('create_time desc');
        $where = array('status' => array('egt',0));
        $list = $Model ->where($where) ->order($order)->limit(5) ->select();  // 交流互动
        $this->assign('list',$list);
        //  建言献策
        $userId = session('userId');
        $map = array(
            'status' => array('eq',0),
        );
        $optionModel = new Opinion();
        $lists = $optionModel->where($map)->order('id desc')->limit(7)->select();
        foreach ($lists as $value) {
            //获取用户信息
            $value['images'] = json_decode($value['images']);
            $value['username'] = $value->user->name;
            if(empty($value['department_name'])){
                $Departid = WechatDepartmentUser::where('userid',$value['create_user'])->order('id desc')->field('departmentid')->find();
                $depart = \app\home\model\WechatDepartment::where('id',$Departid['departmentid'])->field('name')->find();
                $value['department_name'] = $depart['name'];
            }
            ($value->user->header) ? $value['header'] = $value->user->header : $value['header'] = $value->user->avatar;
            //获取相关意见反馈评论
            $map1 = array(
                'aid' => $value['id'],
                'status' => 0,
                'type' => 5,
            );
            $comment = Comment::where($map1)->select();
            foreach ($comment as $k => $val){
                $val['username'] = get_name($val['uid']);
            }
            $value['comment'] = $comment;
            
            //是否点赞
            $map2 = array(
                'aid' => $value['id'],
                'status' => 0,
                'type' => 5,
                'uid' => $userId
            );
            $msg = Like::where($map2)->find();
            if($msg) {
                $value['is_like'] = 1;
            }else{
                $value['is_like'] = 0;
            }
        }
        $this->assign('lists',$lists);
        return $this->fetch();
    }
    /*
     * 交流互动 详情页
     */
    public function detail(){
        $this->anonymous();
        $this->jssdk();

        $id = input('id');
        $userId = session('userId');
        //浏览加一
        $info['views'] = array('exp','`views`+1');
        CompanyModel::where('id',$id)->update($info);

        if($userId != "visitor"){
            //浏览不存在则存入pb_browse表
            $con = array(
                'user_id' => $userId,
                'company_id' => $id,
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
        //详细信息
        $info = CompanyModel::get($id);
        //js分享数据
        $info['link'] = Request::instance()->url(true);
        $info['share_image'] = Request::instance()->domain() . get_cover($info['front_cover'])['path'];
        
        //获取 文章点赞
        $likeModel = new Like;
        $like = $likeModel->getLike(4,$id,$userId);
        $info['is_like'] = $like;
        $this->assign('detail',$info);
        //获取 评论
        $commentModel = new Comment();
        $comment = $commentModel->getComment(4,$id,$userId);
        $this->assign('comment',$comment);
        return $this->fetch();
    }

    /**
     * 加载更多
     */
    public function moreList() {
        $len = input('post.length');
        $type = input('post.type');
        if ($type == 2){
            // 交流互动
            $Model = new CompanyModel();
            $res = $Model->getMoreList($len);
            foreach($res as $value){
                $Depart = \app\home\model\WechatDepartment::where('id',$value['publisher'])->field('name')->find();
                $value['publisher'] = $Depart['name'];
            }
        }else{
            // 建言献策
            $res = Opinion::where(['status' => ['eq',0]])->order('id desc')->limit($len,7)->select();
            foreach($res as $value){
                //获取用户信息
                $value['images'] = json_decode($value['images']);
                if (!empty($value['images'])){
                    $image =array();
                    foreach ($value['images'] as $k=>$val){
                        $img = Picture::get($val);
                        $image[$k] = $img['path'];
                    }
                    $value['images'] = $image;
                }
                $value['username'] = $value->user->name;
                ($value->user->header) ? $value['header'] = $value->user->header : $value['header'] = $value->user->avatar;
                if(empty($value['department_name'])){
                    $Departid = WechatDepartmentUser::where('userid',$value['create_user'])->order('id desc')->field('departmentid')->find();
                    $depart = \app\home\model\WechatDepartment::where('id',$Departid['departmentid'])->field('name')->find();
                    $value['department_name'] = $depart['name'];
                }
                //获取相关意见反馈评论
                $map1 = array(
                    'aid' => $value['id'],
                    'status' => 0,
                    'type' => 5,
                );
                $comment = Comment::where($map1)->select();
                foreach ($comment as $k => $val){
                    $val['username'] = get_name($val['uid']);
                }
                $value['comment'] = $comment;

                //是否点赞
                $map2 = array(
                    'aid' => $value['id'],
                    'status' => 0,
                    'type' => 5,
                );
                $msg = Like::where($map2)->find();
                if($msg) {
                    $value['is_like'] = 1;
                }else{
                    $value['is_like'] = 0;
                }
                $value['time'] = date("Y.m.d",$value['create_time']);
            }
        }
        if($res) {
            return $this->success("加载成功","",$res);
        }else {
            return $this->error("加载失败");
        }
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
                ->order('a.id desc')
                ->find();
            $data['department_name'] = $department['name'];
            $data['images'] = json_encode($data['images']);
            $data['create_user'] = $userId;
            $opinionModel = new Opinion();
            $model = $opinionModel->create($data);
            if($model) {
                $map['status'] = 0;
                return $this->success("提交成功");
            }else{
                return $this->error("提交失败");
            }
        }else{
            return $this->fetch();
        }
    }
}