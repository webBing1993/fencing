<?php
/**
 * Created by PhpStorm.
 * User: 老王
 * Date: 2017/5/10
 * Time: 9:37
 */
namespace app\admin\controller;
use app\admin\model\Culture as CultureModel;
use app\admin\model\Picture;
use com\wechat\TPQYWechat;
use think\Config;
use app\admin\model\Push;
/*
  *  文明创建  控制器
   */
class Culture extends Admin{
    /*
     * 创建要求  主页
     */
    public function index(){
        $map = array(
            'type' => 1, // 创建要求
            'status' => array('egt',0),
        );
        $list = $this->lists('Culture',$map);
        int_to_string($list,array(
            'status' => array(0=>"已发布",1=>"已发布"),
        ));

        $this->assign('list',$list);
        return $this->fetch();
    }
    /*
     * 志愿发布  列表
     */
    public function activity(){
        $map = array(
            'type' => 2, // 志愿发布
            'status' => array('egt',0),
        );
        $list = $this->lists('Culture',$map);
        int_to_string($list,array(
            'status' => array(0=>"已发布",1=>"已发布"),
        ));

        $this->assign('list',$list);
        return $this->fetch();
    }
    /*
     * 志愿情况  列表
     */
    public function volunteer(){
        $map = array(
            'type' => 3, // 志愿情况
            'status' => array('egt',0),
        );
        $list = $this->lists('Culture',$map);
        int_to_string($list,array(
            'recommend' => array(0=>"否",1=>"是"),
            'status' => array(0=>"已发布",1=>"已发布"),
        ));

        $this->assign('list',$list);
        return $this->fetch();
    }
    /*
     * 志愿发布  添加  修改
     */
    public function plus(){
        $id = input('id/d');
        if ($id){
            // 修改
            if (IS_POST){
                $data = input('post.');
                if (empty($data['start_time']) || empty($data['end_time'])){
                    return $this->error('请输入时间字段');
                }
                $data['create_user'] = $_SESSION['think']['user_auth']['id'];
                $cultureModel = new CultureModel();
                $data['start_time'] = strtotime($data['start_time']);
                $data['end_time'] = strtotime($data['end_time']);
                $id = $cultureModel->validate('Culture.others')->save($data,['id' => $data['id']]);
                if($id){
                    return $this->success("修改志愿发布成功",Url('Culture/activity'));
                }else{
                    return $this->get_update_error_msg($cultureModel->getError());
                }
            }else{
                $msg = CultureModel::where('id',$id)->find();
                $this->assign('msg',$msg);
                $this->default_pic();
                return $this->fetch();
            }
        }else{
            // 添加
            if (IS_POST){
                $data = input('post.');
                if (empty($data['id'])){
                    unset($data['id']);
                }
                if (empty($data['start_time']) || empty($data['end_time'])){
                    return $this->error('请输入时间字段');
                }
                $data['create_user'] = $_SESSION['think']['user_auth']['id'];
                $cultureModel = new CultureModel();
                $data['start_time'] = strtotime($data['start_time']);
                $data['end_time'] = strtotime($data['end_time']);
                $id = $cultureModel->validate('Culture.others')->save($data);
                if($id){
                    return $this->success("新增志愿发布成功",Url('Culture/activity'));
                }else{
                    return $this->get_update_error_msg($cultureModel->getError());
                }
            }else{
                $this->default_pic();
                $this->assign('msg','');
                return $this->fetch();
            }
        }
    }
    /*
     * 创建要求  志愿情况   添加
     */
    public function add(){
        if(IS_POST) {
            $data = input('post.');
            $cultureModel = new CultureModel();
            if(empty($data['id'])) {
                unset($data['id']);
            }
            $data['create_user'] = $_SESSION['think']['user_auth']['id'];
            if ($data['type'] == 1){
                $model = $cultureModel->validate('Culture.act')->save($data);
            }else{
                $model = $cultureModel->validate('Culture.other')->save($data);
            }
            if($model){
                if ($data['type'] == 1){
                    return $this->success('新增创建要求成功',Url('Culture/index'));
                }else if($data['type'] == 3){
                    return $this->success('新增志愿情况成功',Url('Culture/volunteer'));
                }
            }else{
                return $this->get_update_error_msg($cultureModel->getError());
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
    /*
     * 创建要求   志愿情况  修改
     */
    public function edit(){
        if(IS_POST) {
            $data = input('post.');
            $cultureModel = new CultureModel();
            if ($data['type'] == 1){
                $model = $cultureModel->validate('Culture.other')->save($data,['id'=> $data['id']]);
            }else{
                $model = $cultureModel->validate('Culture.other')->save($data,['id'=> $data['id']]);
            }
            if($model){
                if ($data['type'] == 1){
                    return $this->success('修改创建要求成功',Url('Culture/index'));
                }else if($data['type'] == 3){
                    return $this->success('修改志愿情况成功',Url('Culture/volunteer'));
                }
            }else{
                return $this->get_update_error_msg($cultureModel->getError());
            }
        }else{
            $this->default_pic();
            $id = input('id');
            $msg = CultureModel::get($id);
            $msg['class'] = 2;
            $msg['type'] = input('type');
            $this->assign('msg',$msg);

            return $this->fetch();
        }
    }
    /*
     * 推送列表
     */
    public function pushlist(){
        if(IS_POST){
            $id = input('id');
            //副图文本周内的新闻消息
            $t = $this->week_time();
            $info = array(
                'id' => array('neq',$id),
                'create_time' => array('egt',$t),
                'status' => 0,
                'type' => ['in',[2,3]]
            );
            $infoes = CultureModel::where($info)->select();
            foreach($infoes as $value){
                if ($value['type'] == 2){
                    $value['title'] = '【志愿发布】'.$value['title'];
                }else{
                    $value['title'] = '【志愿情况】'.$value['title'];
                }
            }
            return $this->success($infoes);
        }else{
            //消息列表
            $map = array(
                'class' => 4,  // 文明创建
                'status' => array('egt',-1),
            );
            $list = $this->lists('Push',$map);
            int_to_string($list,array(
                'status' => array(-1 => '不通过', 0 => '未审核', 1=> '已发送'),
            ));
            //数据重组
            foreach ($list as $value) {
                $msg = CultureModel::where('id',$value['focus_main'])->find();
                $value['title'] = $msg['title'];
            }
            $this->assign('list',$list);
            //主图文本周内的新闻消息
            $t = $this->week_time();    //获取本周一时间
            $info = array(
                'create_time' => array('egt',$t),
                'status' => 0,
                'type' => ['in',[2,3]]
            );
            $infoes = CultureModel::where($info)->select();
            foreach($infoes as $value){
                if ($value['type'] == 2){
                    $value['title'] = '【志愿发布】'.$value['title'];
                }else{
                    $value['title'] = '【志愿情况】'.$value['title'];
                }
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
            $info1 = CultureModel::where('id',$arr1)->find();
        }
        $update['status'] = '1';
        $title1 = $info1['title'];
        CultureModel::where(['id'=>$arr1])->update($update); // 更新推送后的状态
        if ($info1['type'] == 2){
            $pre = '【志愿发布】';
        }else{
            $pre = '【志愿情况】';
        }
        $str1 = strip_tags($info1['content']);
        $des1 = mb_substr($str1,0,40);
        $content1 = str_replace("&nbsp;","",$des1);  //空格符替换成空
        $url1 = "http://tzpb.0571ztnet.com/home/news/detail/id/".$info1['id'].".html";
        $image1 = Picture::get($info1['front_cover']);
        $path1 = "http://tzpb.0571ztnet.com".$image1['path'];
        $information1 = array(
            'title' => $pre . $title1,
            'description' => $content1,
            'url'  => $url1,
            'picurl' => $path1
        );
        $information = array();
        if(!empty($arr2)){
            //副图文信息
            $information2 = array();
            foreach($arr2 as $key=>$value){
                CultureModel::where(['id'=>$value])->update($update); // 更新推送后的状态
                $info2 = CultureModel::where('id',$value)->find();
                $title2 = $info2['title'];
                $str2 = strip_tags($info2['content']);
                if ($info2['type'] == 2){
                    $pre1 = '【志愿发布】';
                }else{
                    $pre1 = '【志愿情况】';
                }
                $des2 = mb_substr($str2,0,40);
                $content2 = str_replace("&nbsp;","",$des2);  //空格符替换成空
                $url2 = "http://tzpb.0571ztnet.com/home/news/detail/id/".$info2['id'].".html";
                $image2 = Picture::get($info2['front_cover']);
                $path2 = "http://tzpb.0571ztnet.com".$image2['path'];
                $information2[] = array(
                    "title" => $pre1 .$title2,
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
            $data['class'] = 4;  // 文明创建
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