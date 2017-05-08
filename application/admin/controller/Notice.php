<?php
/**
 * Created by PhpStorm.
 * User: Lxx<779219930@qq.com>
 * Date: 2016/10/8
 * Time: 10:29
 */

namespace app\admin\controller;

use app\admin\model\Notice as NoticeModel;
use app\admin\model\NoticeSend;
use app\admin\model\Picture;
use app\admin\model\Push;
use app\admin\model\WechatDepartment;
use app\admin\model\WechatTag;
use app\admin\model\WechatUser;
use com\wechat\TPQYWechat;
use think\Config;

/**
 * Class Notice
 * @package 支部活动
 */
class Notice extends Admin {
    /**
     * 相关通知
     * type: 1
     */
    public function index(){
        $map = array(
            'type' => 1,
            'status' => array('egt',0),
        );
        $list = $this->lists('Notice',$map);
        int_to_string($list,array(
            'status' => array(0=>"待审核",1=>"已发布",2=>"审核不通过",3=>"草稿"),
        ));

        $this->assign('list',$list);
        return $this->fetch();
    }

    /**
     * 会议情况
     * type: 2
     */
    public function meet(){
        $map = array(
            'type' => 2,
            'status' => array('egt',0),
        );
        $list = $this->lists('Notice',$map);
        int_to_string($list,array(
            'status' => array(0=>"待审核",1=>"已发布",2=>"审核不通过",3=>"草稿"),
        ));

        $this->assign('list',$list);

        return $this->fetch();
    }

    /**
     * 党课情况
     * type: 3
     */
    public function lecture(){
        $map = array(
            'type' => 3,
            'status' => array('egt',0),
        );
        $list = $this->lists('Notice',$map);
        int_to_string($list,array(
            'status' => array(0=>"待审核",1=>"已发布",2=>"审核不通过",3=>"草稿"),
        ));

        $this->assign('list',$list);

        return $this->fetch();
    }

    /**
     * 活动招募
     * type: 4
     */
    public function recruit(){
        $map = array(
            'type' => 4,
            'status' => array('egt',0),
        );
        $list = $this->lists('Notice',$map);
        int_to_string($list,array(
            'status' => array(0=>"待审核",1=>"已发布",2=>"审核不通过",3=>"草稿"),
        ));

        $this->assign('list',$list);

        return $this->fetch();
    }

    /**
     * 活动情况
     * type: 5
     */
    public function activity(){
        $map = array(
            'type' => 5,
            'status' => array('egt',0),
        );
        $list = $this->lists('Notice',$map);
        int_to_string($list,array(
            'status' => array(0=>"待审核",1=>"已发布",2=>"审核不通过",3=>"草稿"),
        ));

        $this->assign('list',$list);

        return $this->fetch();
    }

    /**
     * 相关通知和活动招募 添加
     */
    public function indexadd(){
        if(IS_POST) {
            $data = input('post.');
            $data['create_user'] = $_SESSION['think']['user_auth']['id'];
            $noticeModel = new NoticeModel();
//            $data['carousel_images'] = json_encode($data['carousel_images']);  //将数组转为字符串存入数据库，用到时解码
            $data['start_time'] = strtotime($data['start_time']);
//            $data['end_time'] = strtotime($data['end_time']);
            $id = $noticeModel->validate('Notice.act')->save($data);
            if($id){
                if($data['type'] == 1){
                    return $this->success("新增相关通知成功",Url('Notice/index'));
                }else{
                    return $this->success("新增活动通知成功",Url('Notice/recruit'));
                }
            }else{
                return $this->error($noticeModel->getError());
            }
        }else {
            $this->default_pic();

            $type = input('type');
            $this->assign('type',$type);
            return $this->fetch();
        }
    }

    /**
     * 相关通知和活动招募 修改
     */
    public function indexedit(){
        if(IS_POST) {
            $data = input('post.');
            $data['create_user'] = $_SESSION['think']['user_auth']['id'];
            $noticeModel = new NoticeModel();
//            $data['carousel_images'] = json_encode($data['carousel_images']);  //将数组转为字符串存入数据库，用到时解码
            $data['start_time'] = strtotime($data['start_time']);
//            $data['end_time'] = strtotime($data['end_time']);
            $id = $noticeModel->validate('Notice.act')->save($data,['id'=>input('id')]);
            if($id){
                if($data['type'] == 1){
                    return $this->success("修改相关通知成功",Url('Notice/index'));
                }else{
                    return $this->success("修改活动招募成功",Url('Notice/recruit'));
                }
            }else{
                return $this->error($noticeModel->getError());
            }
        }else{
            $this->default_pic();

            $id = input('id');
            $msg = NoticeModel::get($id);
            $this->assign('msg',$msg);
            return $this->fetch();
        }
    }

    /**
     * 三个情况添加
     */
    public function add(){
        if(IS_POST) {
            $data = input('post.');
            $noticeModel = new NoticeModel();
            if(empty($data['id'])) {
                unset($data['id']);
            }
            $data['create_user'] = $_SESSION['think']['user_auth']['id'];
//            $data['meet_time'] = strtotime($data['meet_time']);
            $model = $noticeModel->validate('Notice.other')->save($data);
            if($model){
               if ($data['type'] == 3){
                  return $this->success('新增党课情况成功',Url('Notice/lecture'));
               }else if($data['type'] == 5){
                   return $this->success('新增活动情况成功',Url('Notice/activity'));
               }else{
                   return $this->success('新增会议情况成功',Url('Notice/meet'));
               }
            }else{
                 return $this->error($noticeModel->getError());
            }
        }else{
            $this->default_pic();
            $msg = array();
            $msg['type'] = input('type');
            $msg['class'] = 1; // 1为添加 ，2为修改
            $this->assign('msg',$msg);

            return $this->fetch('edit');
        }
    }

    /**
     * 修改
     */
    public function edit(){
        if(IS_POST) {
            $data = input('post.');
            $noticeModel = new NoticeModel();
//            $data['meet_time'] = strtotime($data['meet_time']);
            $model = $noticeModel->validate('Notice.other')->save($data,['id'=> input('id')]);
            if($model){
                if ($data['type'] == 3){
                    return $this->success('修改党课情况成功',Url('Notice/lecture'));
                }else if($data['type'] == 5){
                    return $this->success('修复活动情况成功',Url('Notice/activity'));
                }else{
                    return $this->success('修改会议情况成功',Url('Notice/meet'));
                }
            }else{
                return $this->get_update_error_msg($noticeModel->getError());
            }
        }else{
            $this->default_pic();

            $id = input('id');
            $msg = NoticeModel::get($id);
            $msg['class'] = 2;
            $this->assign('msg',$msg);

            return $this->fetch();
        }
    }

    /**
     * 删除
     */
    public function del(){
        $id = input('id');
        $map['status'] = "-1";
        $info = NoticeModel::where('id',$id)->update($map);
        if($info) {
            return $this->success("删除成功");
        }else{
            return $this->error("删除失败");
        }
    }
    /*
     * 创意组织生活
     *  type:6
     */
    public function regular(){
        $map = array(
            'type' => 6,
            'status' => array('egt',0),
        );
        $list = $this->lists('Notice',$map);
        int_to_string($list,array(
            'status' => array(0=>"待审核",1=>"已发布",2=>"审核不通过",3=>"草稿"),
        ));
        $this->assign('list',$list);

        return $this->fetch();
    }
    /*
     * 创意组织生活  添加 修改
     */
    public function regularadd(){
        $id = input('param.id');
        if($id){
            // 修改
            if(IS_POST){
                $data = input('post.');
                $noticeModel = new NoticeModel();
                $data['create_user'] = $_SESSION['think']['user_auth']['id'];
                $model = $noticeModel->validate('Learn')->where(['id' => $id])->update($data);
                if($model){
                    return $this->success('修改成功',Url("Notice/regular"));
                }else{
                    return $this->error($noticeModel->getError());
                }
            }else{
                $msg = NoticeModel::where(['id' => $id,'status' => 1])->find();
                $this->assign('msg',$msg);
                return $this->fetch();
            }
        }else{
            // 添加
            if(IS_POST){
                $data = input('post.');
                if(empty($data['id'])) {
                    unset($data['id']);
                }
                $noticeModel = new NoticeModel();
                $data['create_user'] = $_SESSION['think']['user_auth']['id'];
                $model = $noticeModel->validate('Learn')->save($data);
                if($model){
                    return $this->success('新增成功!',Url("Notice/regular"));
                }else{
                    return $this->error($noticeModel->getError());
                }
            }else{
                $this->default_pic();
                $this->assign('msg','');
                return $this->fetch();
            }
        }
    }

    public function pushlist() {
        if(IS_POST){
            $id = input('id');
            //副图文本周内的新闻消息
            $t = $this->week_time();
            $info = array(
                'id' => array('neq',$id),
                'create_time' => array('egt',$t),
                'status' => 1,
            );
            $infoes = NoticeModel::where($info)->select();
            int_to_string($infoes,array(
                'type' => array(1=>"上级政策",2=>"会议情况",3=>"党课情况",4=>"活动招募",5=>"活动情况",6=>"创意组织生活"),
            ));
            return $this->success($infoes);
        }else{
            //消息列表
            $map = array(
                'class' => 2,
                'status' => array('egt',-1),
            );
            $list = $this->lists('Push',$map);
            int_to_string($list,array(
                'status' => array(-1 => '不通过', 0 => '未审核', 1=> '已发送'),
            ));
            //数据重组
            foreach ($list as $value) {
                $msg = NoticeModel::where('id',$value['focus_main'])->find();
                $value['title'] = $msg['title'];
            }
            $this->assign('list',$list);
            //主图文本周内的新闻消息
            $t = $this->week_time();    //获取本周一时间
            $info = array(
                'create_time' => array('egt',$t),
                'status' => 1,
            );
            $infoes = NoticeModel::where($info)->select();
            int_to_string($infoes,array(
                'type' => array(1=>"上级政策",2=>"会议情况",3=>"党课情况",4=>"活动招募",5=>"活动情况",6=>"创意组织生活"),
            ));
            $this->assign('info',$infoes);
            return $this->fetch();
        }
    }

    /**
     * 推送
     */
    public function push(){
        $data = input('post.');
        $arr1 = $data['focus_main'];    //主图文id
        isset($data['focus_vice']) ? $arr2 = $data['focus_vice'] : $arr2 = "";    //副图文id
        if($arr1 == -1){
            return $this->error("请选择主图文!");
        }else{
            //主图文信息
            $focus1 = NoticeModel::where('id',$arr1)->find();
            $title1 = $focus1['title'];
            $str1 = strip_tags($focus1['content']);
            $des1 = mb_substr($str1,0,100);
            $content1 = str_replace("&nbsp;","",$des1);  //空格符替换成空
            switch ($focus1['type']) {
                case 1:
                    $url1 = "http://dqpb.0571ztnet.com/home/notice/relevant/id/".$focus1['id'].".html";
                    $pre1 = "【上级政策】";
                    break;
                case 2:
                    $url1 = "http://dqpb.0571ztnet.com/home/notice/meet/id/".$focus1['id'].".html";
                    $pre1 = "【会议情况】";
                    break;
                case 3:
                    $url1 = "http://dqpb.0571ztnet.com/home/notice/party/id/".$focus1['id'].".html";
                    $pre1 = "【党课情况】";
                    break;
                case 4:
                    $url1 = "http://dqpb.0571ztnet.com/home/notice/recruit/id/".$focus1['id'].".html";
                    $pre1 = "【活动招募】";
                    break;
                case 5:
                    $url1 = "http://dqpb.0571ztnet.com/home/notice/activity/id/".$focus1['id'].".html";
                    $pre1 = "【活动情况】";
                    break;
                case 6:
                    $url1 = "http://dqpb.0571ztnet.com/home/notice/regular/id/".$focus1['id'].".html";
                    $pre1 = "【创业组织生活】";
                    break;
                default:
                    break;
            }
            $img1 = Picture::get($focus1['front_cover']);
            $path1 = "http://dqpb.0571ztnet.com".$img1['path'];
            $information1 = array(
                "title" => $pre1.$title1,
                "description" => $content1,
                "url" => $url1,
                "picurl" => $path1,
            );
        }

        $information = array();
        if(!empty($arr2)) {
            //副图文信息
            $information2 = array();
            foreach ($arr2 as $key=>$value){
                $focus = NoticeModel::where('id',$value)->find();
                $title = $focus['title'];
                $str = strip_tags($focus['content']);
                $des = mb_substr($str,0,100);
                $content = str_replace("&nbsp;","",$des);  //空格符替换成空
                switch ($focus['type']) {
                    case 1:
                        $url = "http://dqpb.0571ztnet.com/home/notice/relevant/id/".$focus['id'].".html";
                        $pre = "【上级政策】";
                        break;
                    case 2:
                        $url = "http://dqpb.0571ztnet.com/home/notice/meet/id/".$focus['id'].".html";
                        $pre = "【会议情况】";
                        break;
                    case 3:
                        $url = "http://dqpb.0571ztnet.com/home/notice/party/id/".$focus['id'].".html";
                        $pre = "【党课情况】";
                        break;
                    case 4:
                        $url = "http://dqpb.0571ztnet.com/home/notice/recruit/id/".$focus['id'].".html";
                        $pre = "【活动招募】";
                        break;
                    case 5:
                        $url = "http://dqpb.0571ztnet.com/home/notice/activity/id/".$focus['id'].".html";
                        $pre = "【活动情况】";
                        break;
                    case 6:
                        $url = "http://dqpb.0571ztnet.com/home/notice/regular/id/".$focus['id'].".html";
                        $pre = "【创业组织生活】";
                        break;
                    default:
                        break;
                }
                $img = Picture::get($focus['front_cover']);
                $path = "http://dqpb.0571ztnet.com".$img['path'];
                $info = array(
                    "title" => $pre.$title,
                    "description" => $content,
                    "url" => $url,
                    "picurl" => $path,
                );
                $information2[] = $info;
            }

            //数组合并，主图文放在首位
            foreach ($information2 as $k=>$v){
                $information[0] = $information1;
                $information[$k+1] = $v;
            }
        }else{
            $information[0] = $information1;
        }

        //重组成article数据
        $send = array();
        $re[] = $information;
        foreach ($re as $key => $value){
            $key = "articles";
            $send[$key] = $value;
        }

        //发送给服务号
        $Wechat = new TPQYWechat(Config::get('party'));
        $message = array(
//            'totag' => "18", //审核标签用户
            "touser" => "18768112486",
//            "touser" => "@all",   //发送给全体，@all
            "msgtype" => 'news',
            "agentid" => 17,
            "news" => $send,
            "safe" => "0"
        );
        $msg = $Wechat->sendMessage($message);

        if ($msg['errcode'] == 0){
            $data['focus_vice'] ? $data['focus_vice'] = json_encode($data['focus_vice']) : $data['focus_vice'] = null;
            $data['create_user'] = session('user_auth.username');
            $data['status'] = 1;
            $data['class'] = 2;
            //保存到推送列表
            $s = Push::create($data);
            if ($s){
                return $this->success("发送成功");
            }else{
                return $this->error("发送失败");
            }
        }else{
            return $this->error("发送失败");
        }
    }
}