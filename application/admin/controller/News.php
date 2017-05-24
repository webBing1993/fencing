<?php
/**
 * Created by PhpStorm.
 * User: Lxx<779219930@qq.com>
 * Date: 2016/9/21
 * Time: 14:41
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
 * @package 第一聚焦控制器
 */
class News extends Admin {

    /**
     * 工作部署  主页列表
     */
    public function index(){
        $map = array(
            'type' => 1,
            'status' => array('egt',0),
        );
        $list = $this->lists('News',$map);
        int_to_string($list,array(
            'status' => array(0 =>"已发布",1=>"已发布"),
        ));

        $this->assign('list',$list);

        return $this->fetch();
    }

    /**
     * 中心组学习  主页列表
     */
    public function centre(){
        $map = array(
            'type' => 2,
            'status' => array('egt',0),
        );
        $list = $this->lists('News',$map);
        int_to_string($list,array(
            'status' => array(0 =>"已发布",1=>"已发布"),
            'recommend' => array( 1=>"推荐" , 0=>"不推荐")
        ));

        $this->assign('list',$list);

        return $this->fetch();
    }
    /**
     * 新闻添加
     */
    public function add(){
        $type = input('type');
        if(IS_POST) {
            $data = input('post.');
            $data['create_user'] = $_SESSION['think']['user_auth']['id'];
            if(empty($data['id'])) {
                unset($data['id']);
            }
            $newModel = new NewsModel();
            if ($data['type'] == 1){  // 工作部署
                $info = $newModel->validate('news.act')->save($data);
            }else{
                // 中心组学习
                $info = $newModel->validate('news.other')->save($data);
            }
            if($info) {
                if ($data['type'] == 1){
                    return $this->success("新增成功",Url('News/index'));
                }else{
                    return $this->success("新增成功",Url('News/centre'));
                }
            }else{
                return $this->get_update_error_msg($newModel->getError());
            }
        }else{
            $this->default_pic();
            $this->assign('msg','');
            $this->assign('type',$type);
            return $this->fetch('edit');
        }
    }

    /**
     * 修改
     */
    public function edit(){
        if(IS_POST) {
            $data = input('post.');
            $data['create_time'] = time();
            $newModel = new NewsModel();
            if ($data['type'] == 1){  // 工作部署
                $info = $newModel->validate('news.act')->save($data,['id'=>$data['id']]);
            }else{
                $info = $newModel->validate('news.other')->save($data,['id'=>$data['id']]);
            }
            if($info){
                if ($data['type'] == 1){
                    return $this->success("修改成功",Url("News/index"));
                }else{
                    return $this->success("修改成功",Url('News/centre'));
                }
            }else{
                return $this->get_update_error_msg($newModel->getError());
            }
        }else{
            $this->default_pic();
            $id = input('id/d');
            $type = input('type/d');
            $msg = NewsModel::get($id);
            $this->assign('msg',$msg);
            $this->assign('type',$type);
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
    /*
     * 推送列表
     */
    public function pushlist(){
        if(IS_POST){
            $id = input('id');//主图文id
            //副图文本周内的新闻消息
            $t = $this->week_time();
            $info = array(
                'id' => array('neq',$id),
                'create_time' => array('egt',$t),
                'status' => 0
            );
            $infoes = NewsModel::where($info)->select();
            foreach($infoes as $value){
                if ($value['type'] == 1){
                    $value['title'] = '【工作部署】'.$value['title'];
                }else{
                    $value['title'] = '【中心组学习】'.$value['title'];
                }
            }
            return $this->success($infoes);
        }else{
            //新闻消息列表
            $map = array(
                'class' => 3,  // 党委动态
                'status' => array('egt',-1)
            );
            $list=$this->lists('Push',$map);
            int_to_string($list,array(
                'status' => array(-1 => '不通过', 0 => '未审核', 1=> '已发送')
            ));
            //数据重组
            foreach($list as $value){
                $msg = NewsModel::where('id',$value['focus_main'])->find();
                $value['title'] = $msg['title'];
            }
            $this->assign('list',$list);
            //主图文本周内的新闻消息
            $t = $this->week_time();
            $info = array(
                'create_time' => array('egt',$t),
                'status' => 0
            );
            $infoes = NewsModel::where($info)->select();
            foreach($infoes as $value){
                if ($value['type'] == 1){
                    $value['title'] = '【工作部署】'.$value['title'];
                }else{
                    $value['title'] = '【中心组学习】'.$value['title'];
                }
            }
            $this->assign('info',$infoes);
            return $this->fetch();
        }
    }
    /*
     * 新闻推送
     */
    public function push(){
        $data = input('post.');
        $arr1 = $data['focus_main'];      //主图文id
        isset($data['focus_vice']) ? $arr2 = $data['focus_vice'] : $arr2 = "";  //副图文id
        if($arr1 == -1){
            return $this->error('请选择主图文');
        }else{
            //主图文信息
            $info1 = NewsModel::where('id',$arr1)->find();
        }
        $update['status'] = '1';
        $title1 = $info1['title'];
        NewsModel::where(['id'=>$arr1])->update($update); // 更新推送后的状态
        $str1 = strip_tags($info1['content']);
        $des1 = mb_substr($str1,0,40);
        $content1 = str_replace("&nbsp;","",$des1);  //空格符替换成空
        if ($info1['type'] == 1){
            $pre = '【工作部署】';
            $path1 = hostUrl.'/home/images/common/1.jpg';
        }else{
            $pre = '【中心组学习】';
            $url1 = hostUrl."/home/Dynamic/detail/id/".$info1['id'].".html";
            $image1 = Picture::get($info1['front_cover']);
            $path1 = hostUrl.$image1['path'];
        }
        $information1 = array(
            'title' => $pre.$title1,
            'description' => $content1,
            'url'  => $url1,
            'picurl' => $path1
        );
        $information = array();
        if(!empty($arr2)){
            //副图文信息
            $information2 = array();
            foreach($arr2 as $key=>$value){
                NewsModel::where(['id'=>$value])->update($update); // 更新推送后的状态
                $info2 = NewsModel::where('id',$value)->find();
                $title2 = $info2['title'];
                $str2 = strip_tags($info2['content']);
                $des2 = mb_substr($str2,0,40);
                $content2 = str_replace("&nbsp;","",$des2);  //空格符替换成空
                if ($info2['type'] == 1){
                    $pre1 = '【工作部署】';
                    $path2 = hostUrl.'/home/images/common/1.jpg';
                }else{
                    $pre1 = '【中心组学习】';
                    $url2 = hostUrl."/home/Dynamic/detail/id/".$info2['id'].".html";
                    $image2 = Picture::get($info2['front_cover']);
                    $path2 = hostUrl.$image2['path'];
                }
                $information2[] = array(
                    "title" =>$pre1.$title2,
                    "description" => $content2,
                    "url" => $url2,
                    "picurl" => $path2,
                );
            }
            //数组合并,主图文放在首位
            foreach($information2 as $key=>$value){
                $information[0] = $information1;
                $information[$key+1] = $value;
            }
        }else{
            $information[0] = $information1;
        }
        //重组成article数据
        $send = array();
        $re[] = $information;
        foreach($re as $key => $value){
            $key = "articles";
            $send[$key] = $value;
        }

        //发送给企业号
        $Wechat = new TPQYWechat(Config::get('party'));
        $message = array(
            "touser" => toUser,
            "msgtype" => 'news',
            "agentid" => agentId,  // 消息审核
            "news" => $send,
            "safe" => "0"
        );
        $msg = $Wechat->sendMessage($message);  // 推送至审核

        if($msg['errcode'] == 0){
            $data['focus_vice'] ? $data['focus_vice'] = json_encode($data['focus_vice']) : $data['focus_vice'] = null;
            $data['create_user'] = session('user_auth.username');
            $data['class'] = 3;  // 省委动态
            $data['status'] = 1;
            //保存到推送列表
            $result = Push::create($data);
            if($result){
                return $this->success('发送成功');
            }else{
                return $this->error('发送失败');
            }
        }else{
            return $this->error('发送失败');
        }
    }
}