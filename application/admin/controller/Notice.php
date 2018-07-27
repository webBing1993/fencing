<?php
/**
 * Created by PhpStorm.
 * User: 王泽锋
 * Date: 2018/07/12
 * Time: 14:07
 */

namespace app\admin\controller;

use app\admin\model\Notice as NoticeModel;
use app\admin\model\Picture;

/**
 * Class Notice
 * @package 通知公告
 */
class Notice extends Admin {
    /**
     * 相关通知
     */
    public function index(){
        $map = array(
            'status' => array('egt',0),
        );
        $search = input('search');
        if ($search != '') {
            $map['title'] = ['like', '%' . $search . '%'];
        }
        $list = $this->lists('Notice',$map);
        int_to_string($list,array(
            'status' => array(0=>"已发布",1=>"已发布"),
            'recommend' => array(0=>"否",1=>"是"),
            'push' => array(0=>"否",1=>"是"),
            'type' => array(1=>"训练通知",2=>"赛事通知",3=>"比赛成绩")
        ));

        $this->assign('list',$list);
        return $this->fetch();
    }
    /**
     * 相关通知 添加
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
                $res = $noticeModel->save($data);
                if ($res){
                    return $this->success("添加成功",Url('Notice/index'));
                }else{
                    return $this->error($noticeModel->getError());
                }
            }
        }else {
            return $this->fetch();
        }
    }
    /**
     * 相关通知 修改
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
                $res = $noticeModel->save($data,['id'=>$data['id']]);
                if ($res){
                    return $this->success("修改成功",Url('Notice/index'));
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
        $map['status'] = "-1";
        $info = NoticeModel::where('id',$id)->update($map);
        if($info) {
            return $this->success("删除成功");
        }else{
            return $this->error("删除失败");
        }
    }


    /**
     * 批量删除
     */
    public function moveToTrash()
    {
        $ids = input('ids/a');
        if (!$ids) {
            return $this->error('请勾选删除选项');
        }
        $data['status'] = '-1';
        $info = NoticeModel::where('id', 'in', $ids)->update($data);

        if ($info) {
            return $this->success('批量删除成功', url('index'));
        } else {
            return $this->error('批量删除失败');
        }
    }
}