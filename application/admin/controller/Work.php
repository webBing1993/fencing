<?php
/**
 * Created by PhpStorm.
 * User: 王泽锋
 * Date: 2018/07/17
 * Time: 09:28
 */
namespace app\admin\controller;

use app\admin\model\WechatTag;
use app\admin\model\WechatUser;
use app\home\model\DefinedConfig;
use app\home\model\WechatUserTag;
use app\home\model\WorkRecord;
use think\Controller;
use app\admin\model\Picture;
use app\admin\model\Push;
use com\wechat\TPQYWechat;
use think\Config;

/**
 * Class Work
 * @package  上下班签到控制器
 */
class Work extends Admin {
    /**
     * 主页列表
     */
    public function index(){
        $res = WechatUserTag::where(['tagid' => WechatTag::TAG_7])->select();
        $list = [];
        foreach ($res as $val) {
            $list[] = WechatUser::where(['userid' => $val['userid']])->find();
        }
        $this->assign('list',$list);

        return $this->fetch();
    }

    public function venue(){
        $userId = input('userid');
        $num = 30;
        $weekarray = ['日','一','二','三','四','五','六'];
        $next_day = date('Y-m-d', strtotime('+1 day', time()));

        $list = [];
        for ($i = 0; $i < $num; $i++) {
            $date = date('Y-m-d', strtotime('+'.$i.' days', strtotime($next_day)));
            $list[$i]['name'] = '周'.$weekarray[date("w", strtotime($date))].' · '.$date;
            $list[$i]['date'] = $date;
            $status = WorkRecord::where(['userid' => $userId, 'date' => $date, 'status' => 0])->find();
            $list[$i]['status'] = $status ? 1 : 0;
            $list[$i]['id'] = $status ? $status->id : 0;
        }
        $name = WechatUser::where(['userid' => $userId])->value('name');
        $this->assign('list',$list);
        $this->assign('userid',$userId);
        $this->assign('name',$name);

        return $this->fetch();
    }

    /**
     * 修改
     */
    public function edit(){
        if(IS_POST) {
            $data = input('post.');
            if ($data['status'] == 0) {//删除
                $id = $data['id'];
                $data['status'] = '-1';
                $info = WorkRecord::where('id',$id)->update($data);
                if($info) {
                    return $this->success("排班成功");
                }else{
                    return $this->error("排班失败");
                }
            } else {
                $info['venue_id'] = WechatUserTag::getVenueId($data['id']);
                if (!$info['venue_id']) {
                    return $this->error("请先添加场馆标签");
                }
                $info['userid'] = $data['id'];
                $wechatUserModel = WechatUser::where(['userid' => $info['userid']])->find();
                $info['openid'] = $wechatUserModel['openid'];
                $info['name'] = $wechatUserModel['name'];
                $info['date'] = $data['date'];
                $info['start_time'] = DefinedConfig::where(['defined_key' => 'start_time'])->value('defined_value');
                $info['start_time'] = strtotime($info['date'].' '.$info['start_time']);
                $info['end_time'] = DefinedConfig::where(['defined_key' => 'end_time'])->value('defined_value');
                $info['end_time'] = strtotime($info['date'].' '.$info['end_time']);
                $rs = WorkRecord::where($info)->find();
                if ($rs) {
                    $model = WorkRecord::where(['id'=>$rs['id']])->update(['status'=>0]);
                } else {
                    $info['create_time'] = time();
                    $model = WorkRecord::create($info);
                }
                if ($model) {
                    return $this->success("排班成功");
                } else {
                    return $this->error("排班失败");
                }
            }
        }
    }



}