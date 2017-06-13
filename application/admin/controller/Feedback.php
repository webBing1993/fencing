<?php
/**
 * Created by PhpStorm.
 * User: 老王
 * Date: 2017/3/28
 * Time: 10:37
 */
namespace app\admin\controller;
use app\admin\model\FeedbackResult;
use app\admin\model\WechatDepartment;
use app\admin\model\WechatDepartmentUser;
use app\admin\model\WechatUser;
use app\admin\model\Feedback as FeedbackModel;
use think\Config;
use com\wechat\TPQYWechat;
class Feedback extends Admin{
    /*
     * 意见反馈 列表
     */
    public function index(){
        $map = array(
            'status' => array('egt',0),
        );
        $list = $this->lists('Feedback',$map);
        foreach($list as $value){
            $User = WechatUser::where('userid',$value['userid'])->find();
            $value['name'] = $User['name'];
            $Depart = WechatDepartmentUser::where('userid',$value['userid'])->find();
            $Info = WechatDepartment::where('id',$Depart['departmentid'])->find();
            $value['department'] = $Info['name'];
        }
        $this->assign('list',$list);
        return $this->fetch();
    }
    /*
     * 意见反馈  发布
     */
    public function push(){
        $id = input('post.id');
        $Data = FeedbackModel::where('id',$id)->find();
        $userid = $Data['userid'];
        $content = input('post.content');
        $title = $Data['content'];
        $time = date('Y-m-d',$Data['create_time']);
        $contents = "您于".$time."提交反馈： ".$title."。
        
对您回复： ".$content."。";
        //重组成article数据
        $send = array(
            "content" => $contents
        );
        //发送给企业号
        $Wechat = new TPQYWechat(Config::get('party'));
        $message = array(
            "touser" => $userid,
            "msgtype" => 'text',
            "agentid" => agentId,  // 个人中心
            "text" => $send,
            "safe" => "0"
        );
        $msg = $Wechat->sendMessage($message);  // 推送
        if($msg['errcode'] == 0){
            $data['content'] = $content;
            $data['status'] = 0;
            $data['fid'] = $id;
            //保存到推送列表
            $result = FeedbackResult::create($data);
            if($result){
                FeedbackModel::where('id',$id)->update(array('result'=>'','status'=>1));
                return $this->success('发送成功');
            }else{
                return $this->error('发送失败');
            }
        }else{
            return $this->error('发送失败');
        }
    }
    /*
     * 意见反馈 保存
     */
    public function hold(){
        $id = input('post.id');
        $content = input('post.content');
        $res = FeedbackModel::where('id',$id)->update(array('result'=>$content));
        if ($res){
            return $this->success('保存成功');
        }else{
            return $this->error('保存失败');
        }
    }
    /*
     * 查看 意见反馈 详情页
     */
    public function detail(){
        $id = input('post.id');
        $detail = FeedbackResult::where(['fid' => $id ,'status' => 0])->select();
        foreach($detail as $value){
           $value['create_time'] = date('Y-m-d H:i',$value['create_time']); 
        }
        return $detail;
    }
    /*
     * 意见反馈 删除
     */
    public function del(){
        $id = input('param.id');
        $update['status'] = '-1';
        $res = FeedbackModel::where('id',$id)->update($update);
        if ($res){
            FeedbackResult::where('fid',$id)->update($update);
            return $this->success('删除成功');
        }else{
            return $this->error('删除失败');
        }
    }
}