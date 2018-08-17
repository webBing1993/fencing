<?php
/**
 * Created by PhpStorm.
 * User: 王泽锋
 * Date: 2018/08/14
 * Time: 09:28
 */
namespace app\admin\controller;

use think\Controller;
use app\admin\model\CourseApply as CourseApplyModel;

/**
 * Class CourseApply
 * @package  课程报名控制器
 */
class CourseApply extends Admin {
    /**
     * 主页列表
     */
    public function index(){
        $map = array(
//            'status' => array('egt',0),
        );
        $search = input('search');
        if ($search != '') {
            $map['name|course_name'] = ['like', '%' . $search . '%'];
        }
        $type = (int)input('type');
        if ($type != '') {
            $map['type'] = $type;
        }
        $status = (int)input('status');
        if (input('status') != '') {
            $map['status'] = $status;
        }else{
            $status = null;
        }
        $list = $this->lists('CourseApply',$map);
        $status_list = array(0 =>"未支付",1=>"已支付");
        int_to_string($list,array(
            'type' => array(1 =>"普通课",2 =>"精品课"),
            'status' => $status_list,
        ));
        $this->assign('checkType', $type);
        $this->assign('status_list',$status_list);
        $this->assign('checkStatus', $status);
        $this->assign('list',$list);

        return $this->fetch();
    }

    /**
     * 查看
     */
    public function edit(){
        $id = input('id');
        $msg = CourseApplyModel::get($id);
        $this->assign('msg',$msg);

        return $this->fetch();
    }


}