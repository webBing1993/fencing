<?php
/**
 * Created by PhpStorm.
 * User: 老王
 * Date: 2017/5/11
 * Time: 9:06
 */
namespace app\admin\controller;
use app\admin\model\Push;
use com\wechat\TPQYWechat;
use app\admin\model\Picture;
use think\Config;
use app\admin\model\Paper as PaperModel;
/**
 **文件资料  控制器
 **
 */
class Paper extends Admin{
    /*
     * 文件资料  主页
     */
    public function index(){
        $map = array(
            'status' => array('egt',0),
        );
        $list = $this->lists('Paper',$map);
        int_to_string($list,[
            'status' => [0=>"已发布",1=>"已发布"],
            'recommend' => [0=>'否',1=>'是']
        ]);
        $this->assign('list',$list);
        return $this->fetch();
    }
    /*
     * 文件资料  添加  / 修改
     */
    public function edit(){
        $id = input('id/d');
        if ($id){
            // 修改
            if (IS_POST){
                $data = input('post.');
                $data['create_user'] = $_SESSION['think']['user_auth']['id'];
                $paperModel = new PaperModel();
                $info = $paperModel->validate('paper')->save($data,$data['id']);
                if($info) {
                    return $this->success("修改成功",Url('Paper/index'));
                }else{
                    return $this->get_update_error_msg($paperModel->getError());
                }
            }else{
                $msg = PaperModel::where('id',$id)->find();
                $this->assign('msg',$msg);
                return $this->fetch();
            }
        }else{
            // 添加
            if (IS_POST){
                $data = input('post.');
                $data['create_user'] = $_SESSION['think']['user_auth']['id'];
                if(empty($data['id'])) {
                    unset($data['id']);
                }
                $paperModel = new PaperModel();
                $info = $paperModel->validate('paper')->save($data);
                if($info) {
                    return $this->success("新增成功",Url('Paper/index'));
                }else{
                    return $this->error($paperModel->getError());
                }
            }else{
                $this->default_pic();
                $this->assign('msg','');
                return  $this->fetch();
            }
        }
    }
    /*
     *文件资料  推送列表
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
            $infoes = PaperModel::where($info)->select();
            foreach($infoes as $value){
                $value['title'] = '【文件资料】'.$value['title'];
            }
            return $this->success($infoes);
        }else{
            //新闻消息列表
            $map = array(
                'class' => 5,  // 文件资料
                'status' => array('egt',-1)
            );
            $list=$this->lists('Push',$map);
            int_to_string($list,array(
                'status' => array(-1 => '不通过', 0 => '未审核', 1=> '已发送')
            ));
            //数据重组
            foreach($list as $value){
                $msg = PaperModel::where('id',$value['focus_main'])->find();
                $value['title'] = $msg['title'];
            }
            $this->assign('list',$list);
            //主图文本周内的新闻消息
            $t = $this->week_time();
            $info = array(
                'create_time' => array('egt',$t),
                'status' => 0
            );
            $infoes = PaperModel::where($info)->select();
            foreach($infoes as $value){
                $value['title'] = '【文件资料】'.$value['title'];
            }
            $this->assign('info',$infoes);
            return $this->fetch();
        }
    }
    /*
     * 推送
     */
    public function push(){
        $data = input('post.');
        $arr1 = $data['focus_main'];      //主图文id
        isset($data['focus_vice']) ? $arr2 = $data['focus_vice'] : $arr2 = "";  //副图文id
        if($arr1 == -1){
            return $this->error('请选择主图文');
        }else{
            //主图文信息
            $info1 = PaperModel::where('id',$arr1)->find();
        }
        $update['status'] = '1';
        $title1 = $info1['title'];
        PaperModel::where(['id'=>$arr1])->update($update); // 更新推送后的状态
        $str1 = strip_tags($info1['content']);
        $des1 = mb_substr($str1,0,40);
        $content1 = str_replace("&nbsp;","",$des1);  //空格符替换成空
        $url1 = "http://tzpb.0571ztnet.com/home/paper/detail/id/".$info1['id'].".html";
        $image1 = Picture::get($info1['front_cover']);
        $path1 = "http://tzpb.0571ztnet.com".$image1['path'];
        $information1 = array(
            'title' => '【文件资料】'.$title1,
            'description' => $content1,
            'url'  => $url1,
            'picurl' => $path1
        );
        $information = array();
        if(!empty($arr2)){
            //副图文信息
            $information2 = array();
            foreach($arr2 as $key=>$value){
                PaperModel::where(['id'=>$value])->update($update); // 更新推送后的状态
                $info2 = PaperModel::where('id',$value)->find();
                $title2 = $info2['title'];
                $str2 = strip_tags($info2['content']);
                $des2 = mb_substr($str2,0,40);
                $content2 = str_replace("&nbsp;","",$des2);  //空格符替换成空
                $url2 = "http://tzpb.0571ztnet.com/home/paper/detail/id/".$info2['id'].".html";
                $image2 = Picture::get($info2['front_cover']);
                $path2 = "http://tzpb.0571ztnet.com".$image2['path'];
                $information2[] = array(
                    "title" =>'【文件资料】'.$title2,
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
//            "touser" => "15036667391",
            "totag" => "4",  // 审核组
            "msgtype" => 'news',
            "agentid" => 11,  // 消息审核
            "news" => $send,
            "safe" => "0"
        );
        $msg = $Wechat->sendMessage($message);  // 推送至审核

        if($msg['errcode'] == 0){
            $data['focus_vice'] ? $data['focus_vice'] = json_encode($data['focus_vice']) : $data['focus_vice'] = null;
            $data['create_user'] = session('user_auth.username');
            $data['class'] = 5;  // 文件资料
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