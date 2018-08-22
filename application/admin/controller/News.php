<?php
/**
 * Created by PhpStorm.
 * User: 王泽锋
 * Date: 2018/07/17
 * Time: 09:28
 */
namespace app\admin\controller;

use think\Controller;
use app\admin\model\Picture;
use app\admin\model\Push;
use com\wechat\TPQYWechat;
use app\admin\model\News as NewsModel;
use think\Config;

/**
 * Class News
 * @package  新闻动态   控制器
 */
class News extends Admin {
    /**
     * 主页列表
     */
    public function index(){
        $map = array(
            'status' => array('egt',0),
        );
        $search = input('search');
        if ($search != '') {
            $map['title'] = ['like', '%' . $search . '%'];
        }
        $list = $this->lists('News',$map);
        int_to_string($list,array(
            'status' => array(0 =>"已发布",1=>"已发布"),
            'recommend' => array(0=>"否",1=>"是"),
        ));

        $this->assign('list',$list);

        return $this->fetch();
    }

    /**
     * 新闻添加
     */
    public function add(){
        if(IS_POST) {
            $data = input('post.');
            $data['create_user'] = $_SESSION['think']['user_auth']['id'];
            if(empty($data['id'])){
                unset($data['id']);
            }
            $newModel = new NewsModel();
            $info = $newModel->validate('news')->save($data);
            if($info) {
                if($data['push'] == 1){
                    //推送
                    $nid=$newModel->id;
                    $this->pushto($nid);
                }
                return $this->success("添加成功",Url('News/index'));
            }else{
                return $this->error($newModel->getError());
            }
        }else{
            $this->assign('msg','');

            return $this->fetch('edit');
        }
    }

    /**
     * 修改
     */
    public function edit(){
        if(IS_POST) {
            $data = input('post.');
//            $data['create_time'] = time();
            $newModel = new NewsModel();
            $info = $newModel->validate('news')->save($data,['id'=>input('id')]);
            if($info){
                if($data['push'] == 1){
                    //推送
                    $nid=$newModel->id;
                    $this->pushto($nid);
                }
                return $this->success("修改成功",Url("News/index"));
            }else{
                return $this->get_update_error_msg($newModel->getError());
            }
        }else{
            $id = input('id');
            $msg = NewsModel::get($id);
            $this->assign('msg',$msg);

            return $this->fetch();
        }
    }

    /**
     * 删除功能
     */
    public function del(){
        $id = input('id');
        $data['status'] = '-1';
        $info = NewsModel::where('id',$id)->update($data);
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
        $info = NewsModel::where('id', 'in', $ids)->update($data);

        if ($info) {
            return $this->success('批量删除成功');
        } else {
            return $this->error('批量删除失败');
        }
    }

    //推送给固定人员预览
    public function pushto($nid){
        $httpUrl = config('http_url');
//                $httpUrl="http://".$_SERVER['HTTP_HOST'];
        $arr1 = $nid;    //主图文id
        //主图文信息
        $focus1 = NewsModel::where('id',$arr1)->find();
        $title1 = $focus1['title'];
        $str1 = strip_tags($focus1['content']);
        $des1 = mb_substr($str1,0,100);
        $content1 = str_replace("&nbsp;","",$des1);  //空格符替换成空
        $pre1 = "【新闻动态】";
        $url1 = $httpUrl."/home/association/newsdetail/id/".$focus1['id'].".html";
        $img1 = Picture::get($focus1['front_cover']);
        $path1 = $httpUrl.$img1['path'];
        $send['articles'][0] = array(
            "title" => $pre1.$title1,
            "description" => $content1,
            "url" => $url1,
            "picurl" => $path1,
        );
        //发送给企业号
        $Wechat = new TPQYWechat(Config::get('party'));
        $touser = config('totestuser');
        $newsConf = config('party');
        $message = array(
//          "toparty" => $bm,
            "touser" => $touser,
            "msgtype" => 'news',
            "agentid" => $newsConf['agentid'],
            "news" => $send,
            "safe" => "0"
        );
        $msg = $Wechat->sendMessage($message);
    }

    /**
     * 首页推送按钮
     */
    public function push(){
        $nid = input('id');
        //推送
        $info = $this->pushto2($nid);

        if($info) {
            return $this->success("推送成功");
        }else{
            return $this->error("推送失败");
        }
    }

    //推送给所有人员
    public function pushto2($nid){
        $httpUrl = config('http_url');
//                $httpUrl="http://".$_SERVER['HTTP_HOST'];
        $arr1 = $nid;    //主图文id
        $update['status'] = '1';
        NewsModel::where(['id' => $arr1])->update($update); // 更新推送后的状态
        //主图文信息
        $focus1 = NewsModel::where('id', $arr1)->find();
        $title1 = $focus1['title'];
        $str1 = strip_tags($focus1['content']);
        $des1 = mb_substr($str1, 0, 100);
        $content1 = str_replace("&nbsp;", "", $des1);  //空格符替换成空
        $pre1 = "【新闻动态】";
        $url1 = $httpUrl."/home/association/newsdetail/id/".$focus1['id'].".html";
        $img1 = Picture::get($focus1['front_cover']);
        $path1 = $httpUrl . $img1['path'];
        $send['articles'][0] = array(
            "title" => $pre1 . $title1,
            "description" => $content1,
            "url" => $url1,
            "picurl" => $path1,
        );
        //发送给企业号
        $Wechat = new TPQYWechat(Config::get('party'));
        $touser = config('touser');
        $newsConf = config('party');
        //部门推送
        if ($focus1['push'] == 1) {
            $message = array(
                "touser" => $touser,
                "msgtype" => 'news',
                "agentid" => $newsConf['agentid'],
                "news" => $send,
                "safe" => "0"
            );
            $msg = $Wechat->sendMessage($message);
            if($msg){
                return $this->success("推送成功",'',$msg);
            }else{
                return $this->error("推送失败");
            }
        }
    }


}