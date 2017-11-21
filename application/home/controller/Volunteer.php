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
       //dump($id);exit();
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
        //dump($list);exit();
       /* $Companys = new Companys();
        $mapp = ['status' => ['eq',1],'type'=>['eq',$type]];
        $list = $Companys->get_list($mapp);
        dump($list);exit();*/
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
        //dump($len);exit;
        $type=input('pid');
        //dump($type);exit();
        $map = ['status' => ['eq', 1],'type'=>['eq',$type]];
        $list = $Companys->get_list($map, $len);
        //dump($list);
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
     $this->jssdk();
        $id = input('id');
        if (empty($id)){
            $this ->error('参数错误!');
        }
        $detail = Db::table('pb_companys')->where('id',$id)->where('status',1)->find();
        $this->assign('detail',$detail);

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
            //dump($data);
            //exit();
            //$data['images'] = json_encode($data['front_cover']);
            $data['userid'] = $userId;
            $data['start_time'] = strtotime($data['start_time']);
            $data['create_time'] = strtotime(date("Y-m-d H:i:s"));
            $data['status'] = 0;
            $res = Db::table('pb_company')->insert($data);
            //dump($res);
            //exit();
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
        //$data=Db::table('pb_company')->where('type',1)->where('status',1)->order('create_time desc')->select();
        $Company = new Company();
        $mapp = ['status' => ['eq', 1], 'type' => 1];
        $data = $Company->get_list($mapp);
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
        if ($list) {
            return $this->success('加载成功', '', $list);
        } else {
            return $this->error('加载失败');
        }
    }



    //心愿详情页
    public function wishesdetail(){

        $id=input('id');
        //$type=input('type');
        //dump($type);exit();
        $list3=Db::table('pb_company_recruit')->where('rid',$id)->select();
        if (empty($list3)){
            //dump($id);exit();
            $data = Db::table('pb_company')->where('id', $id)->find();
            $list = Db::table('pb_company_recruit')->where('rid', $id)->select();
            //dump($list);exit();
            foreach ($list as $key => $v) {
                $list2 = Db::table('pb_wechat_user')->where('userid', $v['userid'])->find();
                $list[$key]['userid'] = $list2['name'];
                $list[$key]['image'] = $list2['avatar'];
            }
            //dump($list);exit();
            $this->assign('list', $list);
            $this->assign('data', $data);
            return $this->fetch();
        }else{
            $data = Db::table('pb_company')->where('id', $id)->find();
            $list = Db::table('pb_company_recruit')->where('rid', $id)->select();
            //dump($list);exit();
            foreach ($list as $key => $v) {
                $list2 = Db::table('pb_wechat_user')->where('userid', $v['userid'])->find();
                $list[$key]['userid'] = $list2['name'];
                $list[$key]['image'] = $list2['avatar'];
            }
            //dump($list);exit();
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