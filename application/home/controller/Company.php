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
use app\home\model\Picture;
use app\home\model\Company as CompanyModel;
use app\home\model\WechatUser;
use think\Request;
/*
 * 党员之家
*/

class Company extends Base{
    /*
     * 交流互动   主页
     */
    public function index(){
        $Model = new CompanyModel();
        $order = array('create_time desc');
        $where = array('status' => array('egt',0));
        $list = $Model ->where($where) ->order($order)->limit(5) ->select();
        $this->assign('list',$list);
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
        //是否点赞
        $map2 = array(
            'aid' => $id,
            'status' => 0,
            'type' => 15,
            'uid' => $userId
        );
        $msg = Like::where($map2)->find();
        if($msg) {
            $info['is_like'] = 1;
        }else{
            $info['is_like'] = 0;
        }
        $this->assign('detail',$info);
        //获取 评论
        $commentModel = new Comment();
        $comment = $commentModel->getComment(15,$id,$userId);
        $this->assign('comment',$comment);

        
        return $this->fetch();
    }

    /**
     * 加载更多
     */
    public function moreList() {
        $data = input('post.');
        $Model = new CompanyModel();
        $res = $Model->getMoreList($data);
        //对应的封面id转成为path,时间戳转换
        foreach($res as $k => $v){
            $res[$k]['front_src'] = get_cover($res[$k]['front_cover'])->path;
            $res[$k]['header_src'] = get_cover($res[$k]['image'])->path;
        }
        if($res) {
            return $this->success("加载成功","",$res);
        }else {
            return $this->error("加载失败");
        }
    }
}