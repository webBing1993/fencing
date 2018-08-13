<?php
/**
 * Created by PhpStorm.
 * User: 王泽锋
 * Date: 2018/07/17
 * Time: 09:28
 */
namespace app\admin\controller;

use think\Controller;
use app\admin\model\Picture;
use app\admin\model\Push;
use com\wechat\TPQYWechat;
use app\admin\model\CourseReview as CourseReviewModel;
use app\admin\model\Venue as VenueModel;
use think\Config;

/**
 * Class CourseReview
 * @package  精品课程管理控制器
 */
class CourseReview extends Admin {
    /**
     * 主页列表
     */
    public function index(){
        $map = array(
            'status' => array('egt',-1),
        );
        $search = input('search');
        if ($search != '') {
            $map['course_name'] = ['like', '%' . $search . '%'];
        }
        $list = $this->lists('CourseReview',$map);
        int_to_string($list,array(
            'status' => array(-1 =>"不通过",0 =>"待审核",1=>"通过"),
        ));
        $this->assign('list',$list);

        return $this->fetch();
    }


    /**
     * 审核
     */
    public function del(){
        $data = input('post.');
        $info = CourseReviewModel::update($data);
        if($info) {
            return $this->success("审核成功");
        }else{
            return $this->error("审核失败");
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
        $info = VenueCourseModel::where('id', 'in', $ids)->update($data);

        if ($info) {
            return $this->success('批量删除成功', url('index'));
        } else {
            return $this->error('批量删除失败');
        }
    }
}