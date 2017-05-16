<?php
/**
 * Created by PhpStorm.
 * User: stiff <1570004138@163.com>
 * Date: 2017/5/12
 * Time: 10:50
 */
namespace app\home\controller;
use app\home\model\Culture as CultureModel;
use app\home\model\Browse;
use app\home\model\WechatUser;
use app\home\model\Comment;
use think\Request;
use app\home\model\Like;

class Culture extends Base{
    /**
     * 文明创建
     */
    public function index(){
        $culture = new CultureModel();
        //志愿情况的banner
        $details = $culture ->where(['type' => 3,'status' => 0]) ->order('id desc') ->limit(3) ->select();
        $this ->assign('banner',$details);
        //志愿发布
        $volun = $culture ->where(['type' => 2,'status' => 0]) ->order('id desc') ->limit(2) ->select();
        $this ->assign('volun',$volun);
        //志愿情况
        $volun_details = $culture ->where(['type' => 3,'status' => 0]) ->order('id desc') ->limit(5) ->select();
        $this ->assign('volun_details',$volun_details);
        return $this ->fetch();
    }
    /**
     * 志愿情况加载更多
     */
    public function more(){
        $culture = new CultureModel();
        $len = input('post.');
        if(!empty($len)){
            $volun_details = $culture ->where(['type' => 3,'status' => 0]) ->order('id desc') ->limit($len['length'],5) ->select();
            if(!empty($volun_details)){
                //对应的封面id转成为path,时间戳转换
                foreach($volun_details as $k => $v){
                    $volun_details[$k]['path'] = get_cover($volun_details[$k]['front_cover'])->path;
                    $volun_details[$k]['time'] = date('Y-m-d',$volun_details[$k]['create_time']);
                }
                return $this->success('获取成功',null,$volun_details);
            }else{
                return $this ->error('没有更多数据!');
            }
        }else{
            return $this ->error('参数错误!');
        }
    }
    /**
     * 志愿发布更多
     */
    public function volunlist(){
        $culture = new CultureModel();
        //志愿发布
        $volun = $culture ->where(['type' => 2,'status' => 0]) ->order('id desc') ->limit(7) ->select();
        if(empty($volun)){
            $this ->assign('show',0);

        }else{
            foreach($volun as $k =>$v){
                $time = time();
                if($v['end_time'] > $time){
                    $v['is'] = 0;
                }else{
                    $v['is'] = 1;
                }
            }
            $this ->assign('list',$volun);
            $this ->assign('show',1);
        }
        return $this ->fetch();
    }
    /**
     * 志愿发布加载更多
     */
    public function plus(){
        $culture = new CultureModel();
        $len = input('post.');
        if(!empty($len)){
            $volun_details = $culture ->where(['type' => 2,'status' => 0]) ->order('id desc') ->limit($len['length'],5) ->select();
            if(!empty($volun_details)){
                //对应的封面id转成为path,时间戳转换
                foreach($volun_details as $k => $v){
                    $time = time();
                    if($v['end_time'] > $time){
                        $volun_details[$k]['state'] = 0;
                    }else{
                        $volun_details[$k]['state'] = 1;
                    }
                    $volun_details[$k]['time'] = date('Y-m-d',$volun_details[$k]['create_time']);
                }
                return $this->success('获取成功',null,$volun_details);
            }else{
                return $this ->error('没有更多数据!');
            }
        }else{
            return $this ->error('参数错误!');
        }
    }
    /**
     * 志愿发布详情页
     */
    public function publish(){
        $userId = session('userId');
        $this ->anonymous();
        $this ->jssdk();
        $id = input('get.id');
        $culture = new CultureModel();
        if(!empty($id)){
            //浏览加一
            $info['views'] = array('exp','`views`+1');
            $culture ->where('id',$id) ->update($info);
            if($userId != "visitor"){
                //浏览不存在则存入pb_browse表
                $con = array(
                    'user_id' => $userId,
                    'culture_id' => $id,
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
            //获取 详细信息
            $volun_detail = $culture ->find($id);
            //js分享数据
            $volun_detail['link'] = Request::instance()->url(true);
            $volun_detail['share_image'] = Request::instance()->domain() . get_cover($volun_detail['front_cover'])['path'];
            $this ->assign('list',$volun_detail);
            //获取 评论
            $commentModel = new Comment();
            $comment = $commentModel->getComment(14,$id,$userId);
            $this->assign('comment',$comment);
        }else{
            return $this ->error('参数错误!');
        }
        return $this ->fetch();
    }
    /**
     * 志愿情况详情页
     */
    public function detail(){
        $userId = session('userId');
        $this ->anonymous();
        $this ->jssdk();
        $id = input('get.id');
        $culture = new CultureModel();
        if(!empty($id)){
            //浏览加一
            $info['views'] = array('exp','`views`+1');
            $culture ->where('id',$id) ->update($info);
            if($userId != "visitor"){
                //浏览不存在则存入pb_browse表
                $con = array(
                    'user_id' => $userId,
                    'culture_id' => $id,
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
            //获取 详细信息
            $volun_detail = $culture ->find($id);
            //是否点赞
            $map2 = array(
                'aid' => $id,
                'status' => 0,
                'type' => 14,
            );
            $msg = Like::where($map2)->find();
            if($msg) {
                $volun_detail['is_like'] = 1;
            }else{
                $volun_detail['is_like'] = 0;
            }
            $this ->assign('list',$volun_detail);
            //获取 评论
            $commentModel = new Comment();
            $comment = $commentModel->getComment(14,$id,$userId);
            $this->assign('comment',$comment);
        }else{
            return $this ->error('参数错误!');
        }
        return $this ->fetch();
    }
    
}