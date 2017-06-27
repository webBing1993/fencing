<?php
/**
 * Created by PhpStorm.
 * User: 老王
 * Date: 2017/5/11
 * Time: 14:45
 */
namespace app\admin\controller;
use app\admin\model\Push;
use app\admin\model\WechatDepartment;
use com\wechat\TPQYWechat;
use app\admin\model\Picture;
use think\Config;
use app\admin\model\Company as CompanyModel;
/*
 * 党员之家  控制器
 */
class Company extends Admin{
    /*
     * 交流互动   主页
     */
    public function index(){
        $map = array(
            'status' => array('egt',0),
        );
        $list = $this->lists('Company',$map);
        foreach($list as $value){
            $Department = WechatDepartment::where('id',$value['publisher'])->field('name')->find();
            $value['publisher'] = $Department['name'];
        }
        int_to_string($list,[
            'status' => [0=>"已发布",1=>"已发布"],
        ]);
        $this->assign('list',$list);
        return $this->fetch();
    }
    /*
     * 交流互动  添加 修改
     */
    public function edit(){
        $id = input('get.id/d');
        if ($id){
            // 修改
            if (IS_POST){
                $data = input('post.');
                $data['create_user'] = $_SESSION['think']['user_auth']['id'];
                if ($data['publisher'] == -1){
                    return $this->error("请选择支部发布");
                }
                $companyModel = new CompanyModel();
                $info = $companyModel->validate('company')->save($data,['id' => $data['id']]);
                if($info) {
                    return $this->success("新增成功",Url('Company/index'));
                }else{
                    return $this->get_update_error_msg($companyModel->getError());
                }
            }else{
                $Department = WechatDepartment::where(['parentid' => ['neq',0]])->field('id,name')->select();
                $this->assign('info',$Department);
                $msg = CompanyModel::where('id',$id)->find();
                $this->assign('msg',$msg);
                return $this->fetch();
            }
        }else{
            // 添加
            if (IS_POST){
                $data = input('post.');
                if ($data['publisher'] == -1){
                    return $this->error("请选择支部发布");
                }
                $data['create_user'] = $_SESSION['think']['user_auth']['id'];
                if(empty($data['id'])) {
                    unset($data['id']);
                }
                $companyModel = new CompanyModel();
                $info = $companyModel->validate('company')->save($data);
                if($info) {
                    return $this->success("新增成功",Url('Company/index'));
                }else{
                    return $this->error($companyModel->getError());
                }
            }else{
                $Department = WechatDepartment::where(['parentid' => ['neq',0]])->field('id,name')->select();
                $this->assign('info',$Department);
                $this->assign('msg','');
                return $this->fetch();
            }
        }
    }
    /*
     * 交流互动  删除
     */
    public function del(){
        $id = input('get.id/d');
        if ($id){
            return  $this->error('系统错误,数据不存在');
        }
        $res = CompanyModel::where('id',$id)->update(['status' => -1]);
        if ($res){
            return $this->success('删除成功');
        }else{
            return $this->error('删除失败');
        }
    }
    /*
     * 交流互动   推送列表
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
            $infoes = CompanyModel::where($info)->select();
            foreach($infoes as $value){
                $value['title'] = '【交流互动】'.$value['title'];
            }
            return $this->success($infoes);
        }else{
            //新闻消息列表
            $map = array(
                'class' => 4,  // 党员之家
                'status' => array('egt',-1)
            );
            $list=$this->lists('Push',$map);
            int_to_string($list,array(
                'status' => array(-1 => '不通过', 0 => '未审核', 1=> '已发送')
            ));
            //数据重组
            foreach($list as $value){
                $msg = CompanyModel::where('id',$value['focus_main'])->find();
                $value['title'] = $msg['title'];
            }
            $this->assign('list',$list);
            //主图文本周内的新闻消息
            $t = $this->week_time();
            $info = array(
                'create_time' => array('egt',$t),
                'status' => 0
            );
            $infoes = CompanyModel::where($info)->select();
            foreach($infoes as $value){
                $value['title'] = '【交流互动】'.$value['title'];
            }
            $this->assign('info',$infoes);
            return $this->fetch();
        }
    }
    /*
     * 交流互动 推送
     */
    public function push(){
        $data = input('post.');
        $arr1 = $data['focus_main'];      //主图文id
        isset($data['focus_vice']) ? $arr2 = $data['focus_vice'] : $arr2 = "";  //副图文id
        if($arr1 == -1){
            return $this->error('请选择主图文');
        }else{
            //主图文信息
            $info1 = CompanyModel::where('id',$arr1)->find();
        }
        $update['status'] = '1';
        $title1 = $info1['title'];
        CompanyModel::where(['id'=>$arr1])->update($update); // 更新推送后的状态
        $str1 = strip_tags($info1['content']);
        $des1 = mb_substr($str1,0,40);
        $content1 = str_replace("&nbsp;","",$des1);  //空格符替换成空
        $url1 = hostUrl."/home/Company/detail/id/".$info1['id'].".html";
        $path1 = hostUrl.'/home/images/dangshi/0.jpg';
        $information1 = array(
            'title' => '【交流互动】'.$title1,
            'description' => $content1,
            'url'  => $url1,
            'picurl' => $path1
        );
        $information = array();
        if(!empty($arr2)){
            //副图文信息
            $information2 = array();
            foreach($arr2 as $key=>$value){
                CompanyModel::where(['id'=>$value])->update($update); // 更新推送后的状态
                $info2 = CompanyModel::where('id',$value)->find();
                $title2 = $info2['title'];
                $str2 = strip_tags($info2['content']);
                $des2 = mb_substr($str2,0,40);
                $content2 = str_replace("&nbsp;","",$des2);  //空格符替换成空
                $url2 = hostUrl."/home/Company/detail/id/".$info2['id'].".html";
                $path2 = hostUrl.'/home/images/dangshi/0.jpg';
                $information2[] = array(
                    "title" =>'【交流互动】'.$title2,
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
            "agentid" => agentId,
            "news" => $send,
            "safe" => "0"
        );
        $msg = $Wechat->sendMessage($message);  // 推送至审核

        if($msg['errcode'] == 0){
            $data['focus_vice'] ? $data['focus_vice'] = json_encode($data['focus_vice']) : $data['focus_vice'] = null;
            $data['create_user'] = session('user_auth.username');
            $data['class'] = 4 ;  // 党员之家
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
