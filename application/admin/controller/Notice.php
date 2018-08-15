<?php
/**
 * Created by PhpStorm.
 * User: 王泽锋
 * Date: 2018/07/12
 * Time: 14:07
 */

namespace app\admin\controller;

use app\admin\model\Notice as NoticeModel;
use app\admin\model\Picture;
use app\admin\model\WechatTag;
use com\wechat\TPQYWechat;
use think\Config;

/**
 * Class Notice
 * @package 通知公告
 */
class Notice extends Admin {
    /**
     * 相关通知
     */
    public function index(){
        $map = array(
            'status' => array('egt',0),
        );
        $search = input('search');
        if ($search != '') {
            $map['title'] = ['like', '%' . $search . '%'];
        }
        $list = $this->lists('Notice',$map);
        int_to_string($list,array(
            'status' => array(0=>"已发布",1=>"已发布"),
            'recommend' => array(0=>"否",1=>"是"),
            'push' => array(0=>"否",1=>"是"),
            'type' => array(1=>"训练通知",2=>"赛事通知",3=>"比赛成绩")
        ));

        $this->assign('list',$list);
        return $this->fetch();
    }
    /**
     * 相关通知 添加
     */
    public function indexadd(){
        if(IS_POST) {
            $data = input('post.');
            if($data['push'] == 1){
                if(!empty($data['tag'])){
                    $data['tag'] = json_encode($data['tag']);
                }
            }
            $result = $this->validate($data,'Notice');  // 验证  数据
            $data['create_user'] = $_SESSION['think']['user_auth']['id'];
            if (true !== $result) {
                return $this->error($result);
            }else{
                $noticeModel = new NoticeModel();
                $res = $noticeModel->save($data);
                if ($res){
                    //推送
//                    $nid=$noticeModel->id;
//                    $this->pushto($nid,$data);
                    return $this->success("添加成功",Url('Notice/index'));
                }else{
                    return $this->error($noticeModel->getError());
                }
            }
        }else {
            $list = WechatTag::where('id','>',0)->select();
            $this->assign('list',$list);

            return $this->fetch();
        }
    }
    /**
     * 相关通知 修改
     */
    public function indexedit(){
        if(IS_POST) {
            $data = input('post.');
            $result = $this->validate($data,'Notice');  // 验证  数据
            $data['create_user'] = $_SESSION['think']['user_auth']['id'];
            if (true !== $result) {
                return $this->error($result);
            }else{
                $noticeModel = new NoticeModel();
                $res = $noticeModel->save($data,['id'=>$data['id']]);
                if ($res){
                    return $this->success("修改成功",Url('Notice/index'));
                }else{
                    return $this->get_update_error_msg($noticeModel->getError());
                }
            }
        }else{
            $id = input('id');
            $msg = NoticeModel::get($id);
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


    /**
     * 批量删除
     */
    public function moveToTrash()
    {
        $ids = input('ids/a');
        if (!$ids) {
            return $this->error('请勾选删除选项');
        }
        $data['status'] = '-1';
        $info = NoticeModel::where('id', 'in', $ids)->update($data);

        if ($info) {
            return $this->success('批量删除成功', url('index'));
        } else {
            return $this->error('批量删除失败');
        }
    }


    //添加页面直接推送
    public function pushto($nid,$data){
        if (!empty($data['tag'])) {
            $data['tag'] = json_decode($data['tag']);
        }

        $httpUrl = config('http_url');
//                $httpUrl="http://".$_SERVER['HTTP_HOST'];
        $arr1 = $nid;    //主图文id
        $update['status'] = '0';
        NoticeModel::where(['id'=>$arr1])->update($update); // 更新推送后的状态
        //主图文信息
        $focus1 = NoticeModel::where('id',$arr1)->find();
        $title1 = $focus1['title'];
        $str1 = strip_tags($focus1['content']);
        $des1 = mb_substr($str1,0,100);
        $content1 = str_replace("&nbsp;","",$des1);  //空格符替换成空
        switch ($focus1['type']) {
            case 1:
                $url1 = $httpUrl."/home/association/newsdetail2/id/".$focus1['id'].".html";
                $pre1 = "【训练通知】";
                break;
            case 2:
                $url1 = $httpUrl."/home/association/newsdetail2/id/".$focus1['id'].".html";
                $pre1 = "【赛事通知】";
                break;
            case 3:
                $url1 = $httpUrl."/home/association/newsdetail2/id/".$focus1['id'].".html";
                $pre1 = "【比赛成绩】";
                break;
            default:
                break;
        }
        $img1 = Picture::get($focus1['front_cover']);
        $path1 = $httpUrl.$img1['path'];
        $send['articles'][0] = array(
            "title" => $pre1.$title1,
            "description" => $content1,
            "url" => $url1,
            "picurl" => $path1,
        );
        //发送给企业号
        $Wechat = new TPQYWechat(Config::get('news'));
        $touser = config('touser');
        $newsConf = config('news');


        //部门推送
        if (!empty($focus1['tag'])){
            $bm=join('|',json_decode($focus1['tag'],true));
            $message = array(
                "toparty" => $bm,
                "msgtype" => 'news',
                "agentid" => $newsConf['agentid'],
                "news" => $send,
                "safe" => "0"
            );
            $msg = $Wechat->sendMessage($message);
//
//            $bms=json_decode($focus1['show_depart']);
//
//            foreach($bms as $bmv){
//                $bm2=Db::table('pb_wechat_department_user')->where('departmentid',$bmv)->select();
//
//                foreach($bm2 as $bmv2)
//                {
//                    $mapp22 = ['nid' => $nid, 'userid' => $bmv2['userid'],'type'=>$focus1['type']];
//                    Db::table('pb_noticeqx')->insert($mapp22);
//                }
//            }

        }
    }



}