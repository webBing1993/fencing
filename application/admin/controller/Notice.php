<?php
/**
 * Created by PhpStorm.
 * User: Lxx<779219930@qq.com>
 * Date: 2016/10/8
 * Time: 10:29
 */

namespace app\admin\controller;

use app\admin\model\Notice as NoticeModel;
use app\admin\model\Picture;
use app\admin\model\Push;
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
            'status' => array(0=>"已发布",1=>"已发布"),
        ));

        $this->assign('list',$list);
        return $this->fetch();
    }

    /**
     * 情况 报道
     * type: 2
     */
    public function meet(){
        $map = array(
            'type' => 2,
            'status' => array('egt',0),
        );
        $list = $this->lists('Notice',$map);
        int_to_string($list,array(
            'status' => array(0=>"已发布",1=>"已发布"),
        ));

        $this->assign('list',$list);

        return $this->fetch();
    }

    /**
     * 活动通知
     * type: 3
     */
    public function recruit(){
        $map = array(
            'type' => 3,
            'status' => array('egt',0),
        );
        $list = $this->lists('Notice',$map);
        int_to_string($list,array(
            'status' => array(0=>"已发布",1=>"已发布"),
        ));

        $this->assign('list',$list);

        return $this->fetch();
    }

    /**
     * 活动情况
     * type: 4
     */
    public function activity(){
        $map = array(
            'type' => 4,
            'status' => array('egt',0),
        );
        $list = $this->lists('Notice',$map);
        int_to_string($list,array(
            'status' => array(0=>"已发布",1=>"已发布"),
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
            if (empty($data['start_time']) || empty($data['end_time'])){
                return $this->error('请输入时间字段');
            }
            if (!is_numeric($data['telephone'])){
                return $this->error('联系电话必须为数字');
            }
            $data['create_user'] = $_SESSION['think']['user_auth']['id'];
            $noticeModel = new NoticeModel();
            $data['start_time'] = strtotime($data['start_time']);
            $data['end_time'] = strtotime($data['end_time']);
            if ($data['end_time'] <= $data['start_time']){
                return $this->error('结束时间有错误');
            }
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
            if (empty($data['start_time']) || empty($data['end_time'])){
                return $this->error('请输入时间字段');
            }
            if (!is_numeric($data['telephone'])){
                return $this->error('联系电话必须为数字');
            }
            $data['create_user'] = $_SESSION['think']['user_auth']['id'];
            $noticeModel = new NoticeModel();
            $data['start_time'] = strtotime($data['start_time']);
            $data['end_time'] = strtotime($data['end_time']);
            if ($data['end_time'] <= $data['start_time']){
                return $this->error('结束时间有错误');
            }
            $id = $noticeModel->validate('Notice.act')->save($data,['id'=>input('id')]);
            if($id){
                if($data['type'] == 1){
                    return $this->success("修改相关通知成功",Url('Notice/index'));
                }else{
                    return $this->success("修改活动通知成功",Url('Notice/recruit'));
                }
            }else{
                return $this->get_update_error_msg($noticeModel->getError());
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
            $model = $noticeModel->validate('Notice.other')->save($data);
            if($model){
               if ($data['type'] == 2){
                  return $this->success('新增情况报道成功',Url('Notice/meet'));
               }else if($data['type'] == 4){
                   return $this->success('新增活动情况成功',Url('Notice/activity'));
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
            $model = $noticeModel->validate('Notice.other')->save($data,['id'=> input('id')]);
            if($model){
                if ($data['type'] == 2){
                    return $this->success('修改情况报道成功',Url('Notice/meet'));
                }else if($data['type'] == 4){
                    return $this->success('修复活动情况成功',Url('Notice/activity'));
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
     * 推送 列表
     */
    public function pushlist() {
        if(IS_POST){
            $id = input('id');
            //副图文本周内的新闻消息
            $t = $this->week_time();
            $info = array(
                'id' => array('neq',$id),
                'create_time' => array('egt',$t),
                'status' => 0,
            );
            $infoes = NoticeModel::where($info)->select();
            int_to_string($infoes,array(
                'type' => array(1=>"相关通知",2=>"情况报道",3=>"活动通知",4=>"活动情况"),
            ));
            return $this->success($infoes);
        }else{
            //消息列表
            $map = array(
                'class' => 2,  // 支部活动
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
                'status' => 0,
            );
            $infoes = NoticeModel::where($info)->select();
            int_to_string($infoes,array(
                'type' => array(1=>"相关通知",2=>"情况报道",3=>"活动通知",4=>"活动情况"),
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
            NoticeModel::where('id',$arr1)->update(['status' => 1]);
            $title1 = $focus1['title'];
            $str1 = strip_tags($focus1['content']);
            $des1 = mb_substr($str1,0,100);
            $content1 = str_replace("&nbsp;","",$des1);  //空格符替换成空
            switch ($focus1['type']) {
                case 1:
                    $url1 = hostUrl."/home/notice/relevant/id/".$focus1['id'].".html";
                    $pre1 = "【相关通知】";
                    break;
                case 2:
                    $url1 = hostUrl."/home/notice/meet/id/".$focus1['id'].".html";
                    $pre1 = "【情况报道】";
                    break;
                case 3:
                    $url1 = hostUrl."/home/notice/recruit/id/".$focus1['id'].".html";
                    $pre1 = "【活动通知】";
                    break;
                case 4:
                    $url1 = hostUrl."/home/notice/activity/id/".$focus1['id'].".html";
                    $pre1 = "【活动情况】";
                    break;
                default:
                    break;
            }
            $img1 = Picture::get($focus1['front_cover']);
            $path1 = hostUrl.$img1['path'];
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
                NoticeModel::where('id',$value)->update(['status' => 1]);
                $title = $focus['title'];
                $str = strip_tags($focus['content']);
                $des = mb_substr($str,0,100);
                $content = str_replace("&nbsp;","",$des);  //空格符替换成空
                switch ($focus['type']) {
                    case 1:
                        $url = hostUrl."/home/notice/relevant/id/".$focus['id'].".html";
                        $pre = "【相关通知】";
                        break;
                    case 2:
                        $url = hostUrl."/home/notice/meet/id/".$focus['id'].".html";
                        $pre = "【情况报道】";
                        break;
                    case 3:
                        $url = hostUrl."/home/notice/recruit/id/".$focus['id'].".html";
                        $pre = "【活动通知】";
                        break;
                    case 4:
                        $url = hostUrl."/home/notice/activity/id/".$focus['id'].".html";
                        $pre = "【活动情况】";
                        break;
                    default:
                        break;
                }
                $img = Picture::get($focus['front_cover']);
                $path = hostUrl.$img['path'];
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
            "touser" => toUser,
            "msgtype" => 'news',
            "agentid" => agentId,
            "news" => $send,
            "safe" => "0"
        );
        $msg = $Wechat->sendMessage($message);

        if ($msg['errcode'] == 0){
            $data['focus_vice'] ? $data['focus_vice'] = json_encode($data['focus_vice']) : $data['focus_vice'] = null;
            $data['create_user'] = session('user_auth.username');
            $data['status'] = 1;
            $data['class'] = 2;  // 支部活动
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