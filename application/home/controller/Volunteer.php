<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/10/27
 * Time: 11:02
 */
namespace app\home\controller;
use app\home\model\Volunteer as VolunteerModel;
use app\home\model\VolunteerDetail;
use app\home\model\Company;
use app\home\model\Companyst;
use app\home\model\Companys;
use think\Db;

class Volunteer extends Base{
    /*
     *  志愿之家主页
     * */
    public function index(){

        return $this->fetch();

    }

    /*
     *  志愿风采展
     * */
    public function mien(){
        $Companyst = new Companyst();
        $mapp = ['status' => ['eq', 1]];
        $data = $Companyst->get_list($mapp);
        //循环遍历
        foreach($data as $v){
            $list = Db::table('pb_companys')->where('type',$v['id'])->count();
                $v['number']=$list;
        }
        $this->assign('data',$data);
        return $this->fetch();
    }

    /*
     *  团队介绍
     * */
    public function team(){
       $id = input('id');
        $detail=Db::table('pb_companyst')->where('id',$id)->find();
        $this->assign('detail',$detail);
        return $this->fetch();
    }

    /*
     *  团队风采
     * */
    public function recruit(){
        $type = input('pid');
        //dump($type);exit();
        $list=Db::table('pb_companys')->where('type',$type)->where('status',1)->order('create_time desc')->limit(10)->select();
        $list2=Db::table('pb_companys')->where('type',$type)->where('status',1)->where('istop',1)->limit(3)->select();
        //dump($list2);exit();
        $this->assign('list2',$list2);
        $this->assign('list',$list);
        return $this->fetch();
    }


    /**
     * 志愿者风采type值得活动页面加载更多
     */
    public function lomore()
    {
        $Companys = new Companys();
        $len = input('length');
        $type=input('pid');
        $map = ['status' => ['eq', 1],'type'=>['eq',$type]];
        $list = $Companys->get_list($map, $len);
        if ($list) {
            return $this->success('加载成功', '', $list);
        } else {
            return $this->error('加载失败');
        }
    }

    /*
     *  详情
     * */
    public function detail(){
        $this->anonymous();
        $this->jssdk();
        $id = input('id/d');
        $info = $this->content(8, $id);
        $this->assign('detail', $info);
        return $this ->fetch();
    }

    /**
     * 志愿者风采主题加载更多
     */
    public function more()
    {
        $Companyst = new Companyst();
        $len = input('length');
        $type=input('type');
        $map = ['status' => ['eq', 1]];
        $list = $Companyst->get_list($map, $len);
       //dump($list);exit();
        foreach($list as $v){
            $list2 = Db::table('pb_companys')->where('type',$v['id'])->count();
            $v['number']=$list2;
        }
        //dump($list);exit();

        if ($list) {
            return $this->success('加载成功', '', $list);
        } else {
            return $this->error('加载失败');
        }
    }


    /*
        *  发布和填写
        */
    public  function publish(){
        if (IS_POST) {
            $userId = session('userId');
            $data = input('post.');
            //$data['images'] = json_encode($data['front_cover']);
            $data['front_cover'] = $this->default_pic();
            $data['userid'] = $userId;
            $data['start_time'] = strtotime($data['start_time']);
            $data['create_time'] = strtotime(date("Y-m-d H:i:s"));
            $data['status'] = 0;
            $res = Db::table('pb_company')->insert($data);
            if ($res) {
                return $this->success("发布成功！");
            } else {
                return $this->error('发布失败！');
            }
        } else {
            return $this->fetch();
        }
    }
    /*
     * 点亮微心愿
     * */
    public  function wishes(){
        $Company = new Company();
        $mapp = ['status' => ['eq', 1], 'type' => 1];
        $data = $Company->get_list($mapp);
        foreach($data as $v){
            $list = Db::table('pb_company_recruit')->where('rid',$v['id'])->count();
            $v['receive_number']=$list;
        }
        $type=1;
        $this->assign('type',$type);
        $this->assign('data',$data);
        return $this->fetch();
    }

    /**
     * 微心愿加载更多
     */
    public function vomore()
    {
        $Company = new Company();
        $len = input('length');
       $type=input('type');
        $map = ['status' => ['eq', 1], 'type' =>$type];
        $list = $Company->get_list($map, $len);
        foreach ($list as $v) {
            $list2 = Db::table('pb_company_recruit')->where('rid', $v['id'])->count();
            $v['receive_number'] = $list2;
        }
        if ($list) {
            return $this->success('加载成功', '', $list);
        } else {
            return $this->error('加载失败');
        }
    }



    //心愿详情页
    public function wishesdetail(){

        $id=input('id');
        $list3=Db::table('pb_company_recruit')->where('rid',$id)->select();
        if (empty($list3)){
            $data = Db::table('pb_company')->where('id', $id)->find();
            $list5=Db::table('pb_picture')->where('id', $data['image'])->find();
            $data['image']=$list5['path'];
            $list = Db::table('pb_company_recruit')->where('rid', $id)->select();
            foreach ($list as $key => $v) {
                $list2 = Db::table('pb_wechat_user')->where('userid', $v['userid'])->find();
                $list[$key]['userid'] = $list2['name'];
                $list[$key]['image'] = $list2['avatar'];
            }
            $this->assign('list', $list);
            $this->assign('data', $data);
            return $this->fetch();
        }else{
            $data = Db::table('pb_company')->where('id', $id)->find();
            $list5=Db::table('pb_picture')->where('id', $data['image'])->find();
            $data['image']=$list5['path'];
            $list = Db::table('pb_company_recruit')->where('rid', $id)->select();
            foreach ($list as $key => $v) {
                $list2 = Db::table('pb_wechat_user')->where('userid', $v['userid'])->find();
                $list[$key]['userid'] = $list2['name'];
                $list[$key]['image'] = $list2['avatar'];
            }
            $data['receive_number']=Db::table('pb_company_recruit')->where('rid', $id)->count();
            $this->assign('list', $list);
            $this->assign('data', $data);
            return $this->fetch('Volunteer/wishesdetail2');
        }
    }

    //心愿领取
    public function receive()
    {
            $data1 = input('rid');
            $userId = session('userId');
            $data = ['rid' => $data1, 'userid' => $userId];
            $data['create_time'] = strtotime(date('Y-m-d H:i:s'));
            $list = Db::table('pb_company_recruit')->insert($data);

        $list2 = Db::table('pb_wechat_user')->where('userid',$userId)->find();
        $list2['create_time'] = date('Y-m-d H:i');
        //dump($list2);exit();
            if ($list2) {
                return $this->success('领取成功!', '', $list2);
            } else {
                return $this->error('领取失败!');
            }
    }
    
    /*
     * 志愿者招募
     * */
    public  function  enlist(){
        $Company = new Company();
        $mapp = ['status' => ['eq', 1], 'type' => 2];
        $data = $Company->get_list($mapp);
        foreach($data as $v){
            $list = Db::table('pb_company_recruit')->where('rid',$v['id'])->count();
            $v['receive_number']=$list;
        }
        $type=2;
        $this->assign('type',$type);
        $this->assign('data',$data);
        return $this->fetch();
    }

    /*
      * 志愿者排行
      * */
    public  function  rank(){

        return $this->fetch();
    }



}