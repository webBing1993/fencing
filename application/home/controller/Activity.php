<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/10
 * Time: 9:03
 */

namespace app\home\controller;

use app\home\model\Notice;
use think\Db;

class Activity extends Base
{
    /**
     * @return mixed  主页
     */
    public function index()
    {
        $Notice = new Notice();
        $mapp = ['status' => ['egt', 0], 'type' => 1];
        $leftone = Db::table('pb_notice')->where($mapp)->order('create_time desc')->limit(2)->select();//活动安排
        $mapp = ['status' => ['egt', 0], 'type' => 2];//活动展示
        $lefttwo = $Notice->get_list($mapp);
        $mapp = ['status' => ['egt', 0], 'type' => 3];//会议纪要
        $center = $Notice->get_list($mapp);
        //dump($center);
        //循环遍历三维数组$arr3
        foreach ($center as $v) {
            $list = Db::table('pb_wechat_user')->where('userid', $v['userid'])->find();
            $v['userid'] = $list['name'];

        }
        //dump($center);exit();

        $mapp = ['status' => ['egt', 0], 'type' => 4];//固定活动
        $right = $Notice->get_list($mapp);
        //dump($right);exit();

        $this->assign('leftone', $leftone); // 活动安排
        $this->assign('lefttwo', $lefttwo); // 活动展示
        $this->assign('center', $center); // 会议纪要
        $this->assign('right', $right);  // 固定活动

        return $this->fetch();
    }

    /*
    *  更多活动
    */
    public function morelist()
    {
        $Notice = new Notice();
        $mapp = ['status' => ['egt', 0], 'type' => 1];//活动展示
        $info = $Notice->get_list($mapp);

        $this->assign('info', $info);
        return $this->fetch();
    }

    /**
     * 详情页
     */
    public function detail2()
    {
        $this->anonymous();
        $this->jssdk();
        $id = input('id/d');
        $info = $this->content(4, $id);
        if ($info['front_cover']) {
            $info['front_cover'] = json_decode($info['front_cover']);
        }

        $list = Db::table('pb_wechat_user')->where('userid', $info['userid'])->find();
        $info['userid'] = $list['name'];
        //dump($info);
        //exit();
        //$this->assign('list',$list);
        $this->assign('detail', $info);
        return $this->fetch();

    }

    public function detail()
    {
        $this->anonymous();
        $this->jssdk();
        $id = input('id/d');
        $info = $this->content(4, $id);
        //dump($info);
        //exit();
        $this->assign('detail', $info);
        return $this->fetch();

    }

    /**
     * 列表加载更多
     */
    public function more()
    {
        $Notice = new Notice();
        $len = input('length');
        $c = input('type');
        if ($c == 0) {
            $type = 2;  //活动展示
        } elseif ($c == 1) {
            $type = 3; //会议纪要
        } else {
            $type = 4;//固定活动
        }
        $map = ['status' => ['egt', 0], 'type' => $type];
        $list = $Notice->get_list($map, $len);
        if ($list) {
            return $this->success('加载成功', '', $list);
        } else {
            return $this->error('加载失败');
        }
    }

    /*
     *  发布和填写
     */
    public function publish()
    {
        if (IS_POST) {
            $userId = session('userId');
            $data = input('post.');
            //print_r($data);
            //exit();
            $data['front_cover'] = json_encode($data['front_cover']);
            $result = $this->validate($data, 'Publish');  // 验证  数据
            $data['userid'] = $userId;
            /*$data['create_user'] = $_SESSION['think']['user_auth']['id'];*/
            if (true !== $result) {
                return $this->error($result);
            } else {
                $data['start_time'] = strtotime($data['start_time']);
                $data['create_time'] = strtotime(date("Y-m-d H:i:s"));
                $res = Db::table('pb_notice')->insert($data);

                if ($res) {
                    return $this->success("发布成功！");
                } else {
                    return $this->error('发布失败！');
                }
            }
        } else {
            return $this->fetch();
        }
    }


}