<?php
/**
 * Created by PhpStorm.
 * User: 老王
 * Date: 2017/5/10
 * Time: 9:37
 */
namespace app\admin\controller;
use app\admin\model\Culture as CultureModel;
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
                'class' => 2,  // 支部活动
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
}