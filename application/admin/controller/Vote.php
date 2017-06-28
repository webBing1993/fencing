<?php
/**
 * Created by PhpStorm.
 * User: 老王
 * Date: 2017/1/5
 * Time: 15:48
 */
namespace app\admin\controller;
use app\admin\model\Vote as VoteModel;
use app\admin\model\VoteOptions;
use app\admin\model\WechatDepartment;
use com\wechat\TPQYWechat;
use app\admin\model\Picture;
use app\admin\model\Push;
use think\Config;
/*
 *  选举投票
*/
class Vote extends Admin{
    /*
     * 投票管理  主页
     */
    public function index(){
        $map = array(
            'status' => array('egt',0),
        );
        $list = $this->lists('Vote',$map);
        foreach($list as $value){
            $Department = WechatDepartment::where('id',$value['publisher'])->field('name')->find();
            $value['publisher'] = $Department['name'];
        }
        int_to_string($list,array(
            'status' => array(0=>"已发布",1=>"已发布"),
        ));
        $this->assign('list',$list);
        return $this->fetch();
    }
    /*
     * 投票主题添加 / 修改 
     */
    public function add(){
        $id = input('id');
        if ($id){
            // 修改
            if(IS_POST){
                //  修改 保存
                $data = input('post.');
                $data['update_user'] = $_SESSION['think']['user_auth']['id'];
                $date['update_time'] = time();
                $data['end_time'] = strtotime($data['end_time']);
                $Vote = new VoteModel();
                // 添加保存 题目
                $model = $Vote->validate('Vote.act')->save($data,['id' => $id]);
                if($model){
                    // 添加保存 选项
                    return $this->success("修改投票主题成功",Url('Vote/index'));
                }else{
                    return $this->error($Vote->getError());
                }
            }else{
                $Department = WechatDepartment::where(['parentid' => ['neq',0]])->field('id,name')->select();
                $this->assign('info',$Department);
                $Vote = VoteModel::where('id',$id)->find();
                $this->assign('msg',$Vote);
                return  $this->fetch();
            }
        }else{
            //添加
            if(IS_POST){
                //  添加 保存
                $data = input('post.');
                $data['create_user'] = $_SESSION['think']['user_auth']['id'];
                if (empty($data['end_time'])){
                    return $this->error('请添加截止时间');
                }
                $data['end_time'] = strtotime($data['end_time']);
                $Vote = new VoteModel();
                // 添加保存 题目
                $model = $Vote->validate('Vote.act')->save($data);
                if($model){
                    // 添加保存 选项
                    return $this->success("新增投票主题成功",Url('Vote/index'));
                }else{
                    return $this->error($Vote->getError());
                }
            }else{
                $Department = WechatDepartment::where(['parentid' => ['neq',0]])->field('id,name')->select();
                $this->assign('info',$Department);
                $this->assign('msg','');
                return  $this->fetch();
            }
        }
    }
    /*
     * 投票主题  删除
     */
    public function del(){
        $id = input('id');
        $info['status'] = -1;
        $res = VoteModel::where('id',$id)->update($info);  // 删除投票主题
        if ($res){
            $num = VoteOptions::where(['vote_id' => $id,'status' => 0])->count();  // 删除相应的选项
            if ($num == 0){
                return $this->success('删除成功');
            }else{
                $result = VoteOptions::where('vote_id',$id)->update($info);
                if ($result){
                    return $this->success('删除成功');
                }else{
                    return $this->error('删除失败');
                }
            }
        }else{
            return $this->error('删除失败');
        }
    }
    /*
     * 选项详情 列表
     */
    public function options(){
        $id = input('id');
        if ($id){
            session('vid',$id);
        }else{
            session('vid','');
        }
        $map = array(
            'vote_id' => $id,
            'status' => array('egt',0),
        );
        $list = $this->lists('VoteOptions',$map,['num'=>'desc']);
        int_to_string($list,array(
            'status' => array(0=>"已发布"),
        ));
        $this->assign('list',$list);
        $this->assign('vid',$id);  // 投票主题id
        return $this->fetch();
    }
    /*
     * 投票选项  添加 / 修改
     */
    public function edit(){
        $id = input('param.id');
        input('param.vid')?$vote_id = input('param.vid') : $vote_id = session('vid');
        if ($id){
            // 修改
            if(IS_POST){
                //  添加 保存
                $data = input('post.');
                $data['update_user'] = $_SESSION['think']['user_auth']['id'];
                $data['update_time'] = time();
                $Options = new VoteOptions();
                // 添加保存 题目
                $model = $Options->validate('Vote.other')->save($data,['id' => $id]);
                if($model){
                    // 添加保存 选项
                    return $this->success("修改投票选项成功",Url('Vote/options?id='.$vote_id));
                }else{
                    return $this->error($Options->getError());
                }
            }else{
                $Options = VoteOptions::where('id',$id)->find();
                $this->assign('msg',$Options);
                return  $this->fetch();
            }
        }else{
            //添加
            if(IS_POST){
                //  添加 保存
                $data = input('post.');
                $data['create_user'] = $_SESSION['think']['user_auth']['id'];
                $data['vote_id'] = $vote_id;
                $Options = new VoteOptions();
                // 添加保存 题目
                $model = $Options->validate('Vote.other')->save($data);
                if($model){
                    // 添加保存 选项
                    return $this->success("新增投票选项成功",Url('Vote/options?id='.$vote_id));
                }else{
                    return $this->error($Options->getError());
                }
            }else{
                $this->assign('msg','');
                return  $this->fetch();
            }
        }
    }
    /*
     * 选项删除
     */
    public function optionsdel(){
        $id = input('id');
        $info['status'] = -1;
        $res = VoteOptions::where('id',$id)->update($info);  // 删除投票主题
        if ($res){
            return $this->success('删除成功');
        }else{
            return $this->error('删除失败');
        }
    }
    /*
    * 民主评议   推送列表
    */
    public function pushlist(){
        //新闻消息列表
        $map = array(
            'class' => 5,  // 民主评议
            'status' => array('egt',-1)
        );
        $list=$this->lists('Push',$map);
        int_to_string($list,array(
            'status' => array(-1 => '不通过', 0 => '未审核', 1=> '已发送')
        ));
        //数据重组
        foreach($list as $value){
            $msg = VoteModel::where('id',$value['focus_main'])->find();
            $value['title'] = $msg['title'];
        }
        $this->assign('list',$list);
        //主图文本周内的新闻消息
        $t = $this->week_time();
        $info = array(
            'create_time' => array('egt',$t),
            'end_time' => ['gt',time()],
            'status' => 0
        );
        $infoes = VoteModel::where($info)->select();
        foreach($infoes as $value){
            $value['title'] = '【民主评议】'.$value['title'];
        }
        $this->assign('info',$infoes);
        return $this->fetch();
    }
    /*
     * 民主评议  推送
     */
    public function push(){
        $data = input('post.');
        $arr = $data['focus_main'];      //主图文id
        if($arr == -1){
            return $this->error('请选择主图文');
        }else{
            //主图文信息
            $info = VoteModel::where('id',$arr)->find();
        }
        $update['status'] = '1';
        $title = $info['title'];
        VoteModel::where(['id'=>$arr])->update($update); // 更新推送后的状态
        $content = $info['awards'];  //空格符替换成空
        $url = hostUrl."/home/Vote/vote/type/1/id/".$info['id'].".html";
        $img = Picture::get($info['front_cover']);
        $path = hostUrl.$img['path'];
        $information = array(
            'title' => '【民主评议】'.$title,
            'description' => $content,
            'url'  => $url,
            'picurl' => $path
        );
        //重组成article数据
        $send = array(
            'articles' => $information
        );
        //发送给企业号
        $Wechat = new TPQYWechat(Config::get('party'));
        $message = array(
            "toparty" => $info['publisher'],
//        'touser' => toUser,
            "msgtype" => 'news',
            "agentid" => agentId,
            "news" => $send,
            "safe" => "0"
        );
        $msg = $Wechat->sendMessage($message);  // 推送至审核
        if($msg['errcode'] === 0){
            $data['focus_vice'] = null;
            $data['create_user'] = session('user_auth.username');
            $data['class'] = 5 ;  // 民主评议
            $data['status'] = 1;
            //保存到推送列表
            $result = Push::create($data);
            if($result){
                return $this->success('发送成功');
            }else{
                return $this->error('发送失败');
            }
        }else{
            return $this->error($Wechat->errMsg.'发送失败');
        }
    }
}