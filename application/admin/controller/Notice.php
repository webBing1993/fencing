<?php
/**
 * Created by PhpStorm.
 * User: Lxx<779219930@qq.com>
 * Date: 2016/10/8
 * Time: 10:29
 */

namespace app\admin\controller;

use app\admin\model\Notice as NoticeModel;
use app\admin\model\Picture;
use app\admin\model\Push;
use com\wechat\TPQYWechat;
use think\Config;

/**
 * Class Notice
 * @package  支部活动
 */
class Notice extends Admin {
    /**
     * 活动安排
     */
    public function index(){
        $map = array(
            'type' => 1,
            'status' => array('egt',1),
        );
        $list = $this->lists('Notice',$map);
        int_to_string($list,array(
            'status' => array(1=>"已发布"),
            'recommend' => array(0 => "否" ,1 => "是")
        ));
        $this->assign('list',$list);
        return $this->fetch();
    }
    /**
     * 活动安排 添加
     */
    public function indexadd(){
        if(IS_POST) {
            $data = input('post.');
            $result = $this->validate($data,'Notice');  // 验证  数据
            $data['create_user'] = $_SESSION['think']['user_auth']['id'];
            if (true !== $result) {
                return $this->error($result);
            }else{
                $noticeModel = new NoticeModel();
                $data['start_time'] = strtotime($data['start_time']);
                $data['end_time'] = strtotime($data['end_time']);
                if (!empty($data['start_time']) && empty($data['end_time'])){
                    return $this->error('请添加结束时间');
                }
                if (empty($data['start_time']) && !empty($data['end_time'])){
                    return $this->error('请添加开始时间');
                }
                if (!empty($data['start_time']) && !empty($data['end_time']) && $data['end_time'] <= $data['start_time']){
                    return $this->error('结束时间有错误');
                }
                $res = $noticeModel->save($data);
                if ($res){
                    return $this->success("新增安排成功",Url('Notice/index'));
                }else{
                    return $this->error($noticeModel->getError());
                }
            }
        }else {
            return $this->fetch();
        }
    }
    /**
     * 活动安排 修改
     */
    public function indexedit(){
        if(IS_POST) {
            $data = input('post.');
            $result = $this->validate($data,'Notice');  // 验证  数据
            $data['create_user'] = $_SESSION['think']['user_auth']['id'];
            if (true !== $result) {
                return $this->error($result);
            }else{
                $noticeModel = new NoticeModel();
                if (!empty($data['start_time']) && empty($data['end_time'])){
                    return $this->error('请添加结束时间');
                }
                if (empty($data['start_time']) && !empty($data['end_time'])){
                    return $this->error('请添加开始时间');
                }
                if (!empty($data['start_time']) && !empty($data['end_time']) && $data['end_time'] <= $data['start_time']){
                    return $this->error('结束时间有错误');
                }
                $data['start_time'] = strtotime($data['start_time']);
                $data['end_time'] = strtotime($data['end_time']);
                $res = $noticeModel->save($data,['id'=>$data['id']]);
                if ($res){
                    return $this->success("修改安排成功",Url('Notice/index'));
                }else{
                    return $this->get_update_error_msg($noticeModel->getError());
                }
            }
        }else{
            $id = input('id');
            $msg = NoticeModel::get($id);
            $this->assign('msg',$msg);
            return $this->fetch();
        }
    }
    /**
     * 删除
     */
    public function del(){
        $id = input('id');
        if (empty($id)){
            return $this->error('系统参数错误');
        }
        $map['status'] = "-1";
        $info = NoticeModel::where('id',$id)->update($map);
        if($info) {
            return $this->success("删除成功");
        }else{
            return $this->error("删除失败");
        }
    }
}