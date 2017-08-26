<?php
/**
 * Created by PhpStorm.
 * User: sitff
 * Date: 2017/8/26
 * Time: 上午9:52
 */
namespace app\home\controller;
use app\home\model\Like;
use app\home\model\Comment;

class Live extends Base
{
    /**
     *
     * @return mixed
     */
    public function index()
    {

        $this->anonymous();

        $userId = session('userId');

        if($userId != "visitor"){
            //浏览不存在则存入pb_browse表
//            $con = array(
//                'user_id' => $userId,
//                'news_id' => $id,
//            );
//            $history = Browse::get($con);
//            if(!$history && $id != 0){
//                $s['score'] = array('exp','`score`+1');
//                if ($this->score_up()){
//                    // 未超过 15分
//                    WechatUser::where('userid',$userId)->update($s);
//                    Browse::create($con);
//                }
//            }
        }


        //获取 评论
        $commentModel = new Comment();
        $comment = $commentModel->getComment(12,1,$userId);
        $map = array(
            'type' => 12,
            'aid' => 1,
            'status' => 0,
            'create_time' => ['egt',strtotime(date('Y-m-d'))]
        );

        $comments = $commentModel->where($map)->count();

        $this->assign('live_path',config('live_path'));
        $this->assign('comments',$comments);
        $this->assign('comment',$comment);

        return $this->fetch();
    }

}