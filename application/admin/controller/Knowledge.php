<?php
/**
 * Created by PhpStorm.
 * User: 王泽锋
 * Date: 2018/07/17
 * Time: 09:41
 */
namespace app\admin\controller;

use think\Controller;
use app\admin\model\Picture;
use app\admin\model\Push;
use com\wechat\TPQYWechat;
use app\admin\model\Knowledge as KnowledgeModel;
use think\Config;

/**
 * Class Knowledge
 * @package  击剑知识   控制器
 */
class Knowledge extends Admin {
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
        $list = $this->lists('Knowledge',$map);
        int_to_string($list,array(
            'type' => array(1 =>"重剑",2=>"花剑",3=>"佩剑"),
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
            $newModel = new KnowledgeModel();
            $info = $newModel->validate('Knowledge')->save($data);
            if($info) {
                return $this->success("添加成功",Url('Knowledge/index'));
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
            $newModel = new KnowledgeModel();
            $info = $newModel->validate('Knowledge')->save($data,['id'=>input('id')]);
            if($info){
                return $this->success("修改成功",Url("Knowledge/index"));
            }else{
                return $this->get_update_error_msg($newModel->getError());
            }
        }else{
            $id = input('id');
            $msg = KnowledgeModel::get($id);
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
        $info = KnowledgeModel::where('id',$id)->update($data);
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
        $info = VenueModel::where('id', 'in', $ids)->update($data);

        if ($info) {
            return $this->success('批量删除成功', url('Venue/index'));
        } else {
            return $this->error('批量删除失败');
        }
    }

//    /*
//     * 推送列表
//     */
//    public function pushlist(){
//        if(IS_POST){
//            $id = input('id');//主图文id
//            //副图文本周内的新闻消息
//            $t = $this->week_time();
//            $info = array(
//                'id' => array('neq',$id),
//                'create_time' => array('egt',$t),
//                'status' => 0
//            );
//            $infoes = NewsModel::where($info)->select();
//            foreach($infoes as $value){
//                $value['title'] = '【党建动态】'.$value['title'];
//                /*switch ($value['type']){
//                    case 1:
//                        $value['title'] = '【新闻聚焦】'.$value['title'];
//                        break;
//                    case 2:
//                        $value['title'] = '【各地动态】'.$value['title'];
//                        break;
//                    case 3:
//                        $value['title'] = '【政策文件】'.$value['title'];
//                        break;
//                    default;
//                }*/
//            }
//            return $this->success($infoes);
//        }else{
//            //新闻消息列表
//            $map = array(
//                'class' => 1,  // 党建动态
//                'status' => array('egt',-1)
//            );
//            $list=$this->lists('Push',$map);
//            int_to_string($list,array(
//                'status' => array(-1 => '不通过', 0 => '未审核', 1=> '已发送')
//            ));
//            //数据重组
//            foreach($list as $value){
//                $msg = NewsModel::where('id',$value['focus_main'])->find();
//                $value['type'] = '党建动态';
//                /*switch ($msg['type']){
//                    case 1:
//                        $value['type'] = '新闻聚焦';
//                        break;
//                    case 2:
//                        $value['type'] = '各地动态';
//                        break;
//                    case 3:
//                        $value['type'] = '政策文件';
//                        break;
//                    default;
//                }*/
//                $value['title'] = $msg['title'];
//            }
//            $this->assign('list',$list);
//            //主图文本周内的新闻消息
//            $t = $this->week_time();
//            $info = array(
//                'create_time' => array('egt',$t),
//                'status' => 0
//            );
//            $infoes = NewsModel::where($info)->select();
//            foreach($infoes as $value){
//                $value['title'] = '【党建动态】'.$value['title'];
//                /*switch ($value['type']){
//                    case 1:
//                        $value['title'] = '【新闻聚焦】'.$value['title'];
//                        break;
//                    case 2:
//                        $value['title'] = '【各地动态】'.$value['title'];
//                        break;
//                    case 3:
//                        $value['title'] = '【政策文件】'.$value['title'];
//                        break;
//                    default;
//                }*/
//            }
//            $this->assign('info',$infoes);
//            return $this->fetch();
//        }
//    }
//    /*
//     * 新闻推送
//     */
//    public function push(){
//        $data = input('post.');
//        $httpUrl = config('http_url');
//        $arr1 = $data['focus_main'];      //主图文id
//        isset($data['focus_vice']) ? $arr2 = $data['focus_vice'] : $arr2 = "";  //副图文id
//        if($arr1 == -1){
//            return $this->error('请选择主图文');
//        }else{
//            //主图文信息
//            $info1 = NewsModel::where('id',$arr1)->find();
//        }
//        $update['status'] = '1';
//        $title1 = $info1['title'];
//        NewsModel::where(['id'=>$arr1])->update($update); // 更新推送后的状态
//        $str1 = strip_tags($info1['content']);
//        $des1 = mb_substr($str1,0,40);
//        $content1 = str_replace("&nbsp;","",$des1);  //空格符替换成空
//        $pre = '【党建动态】';
//        $url1 = $httpUrl."/home/News/detail/id/".$info1['id'].".html";
//        $image1 = Picture::get($info1['front_cover']);
//        $path1 = $httpUrl.$image1['path'];
//        $information1 = array(
//            'title' => $pre.$title1,
//            'description' => $content1,
//            'url'  => $url1,
//            'picurl' => $path1
//        );
//        $information = array();
//        if(!empty($arr2)){
//            //副图文信息
//            $information2 = array();
//            foreach($arr2 as $key=>$value){
//                NewsModel::where(['id'=>$value])->update($update); // 更新推送后的状态
//                $info2 = NewsModel::where('id',$value)->find();
//                $title2 = $info2['title'];
//                $str2 = strip_tags($info2['content']);
//                $des2 = mb_substr($str2,0,40);
//                $content2 = str_replace("&nbsp;","",$des2);  //空格符替换成空
//                $pre1 = '【党建动态】';
//                $url2 = $httpUrl."/home/News/detail/id/".$info2['id'].".html";
//                $image2 = Picture::get($info2['front_cover']);
//                $path2 = $httpUrl.$image2['path'];
//                $information2[] = array(
//                    "title" =>$pre1.$title2,
//                    "description" => $content2,
//                    "url" => $url2,
//                    "picurl" => $path2,
//                );
//            }
//            //数组合并,主图文放在首位
//            foreach($information2 as $key=>$value){
//                $information[0] = $information1;
//                $information[$key+1] = $value;
//            }
//        }else{
//            $information[0] = $information1;
//        }
//        //重组成article数据
//        $send = array();
//        $re[] = $information;
//        foreach($re as $key => $value){
//            $key = "articles";
//            $send[$key] = $value;
//        }
//
//        //发送给企业号
//        $Wechat = new TPQYWechat(Config::get('news'));
//        $touser = config('touser');
//        $newsConf = config('news');
//        $message = array(
//            "touser" => $touser, //发送给全体，@all
//            "msgtype" => 'news',
//            "agentid" => $newsConf['agentid'],
//            "news" => $send,
//            "safe" => "0"
//        );
//        $msg = $Wechat->sendMessage($message);  // 推送至审核
//
//        if($msg['errcode'] == 0){
//            $data['focus_vice'] ? $data['focus_vice'] = json_encode($data['focus_vice']) : $data['focus_vice'] = null;
//            $data['create_user'] = session('user_auth.username');
//            $data['class'] = 1;  // 党建动态
//            $data['status'] = 1;
//            //保存到推送列表
//            $result = Push::create($data);
//            if($result){
//                return $this->success('发送成功');
//            }else{
//                return $this->error('发送失败');
//            }
//        }else{
//            return $this->error('发送失败');
//        }
//    }
}