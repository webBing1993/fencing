<?php
namespace app\admin\controller;

/**
 * Created by PhpStorm.
 * User: 老王
 * Date: 2017/7/18
 * Time: 8:58
 */
use app\admin\model\Work as WorkModel;
use think\Db;
use think\Controller;
use think\Url;

/**
 * Class Work
 * @package app\admin\controller  签到管理  控制器
 */
class Work extends Admin
{
    /*
     * 主页
     */
    public function index()
    {
        $map = array(
            'status' => array('eq', 0),
        );
        $list = $this->lists('Work', $map);
        int_to_string($list, array(
            'type' => array(1 => "三会一课", 2 => "志愿之家"),
        ));
        //dump($list);
        //exit();

        $this->assign('list', $list);
        return $this->fetch();
    }

    /*
     *  删除签到标题
     */
    public function del($id)
    {
        $data = Db::table('pb_work')->where('id', $id)->update(['status' => '1']);
        if ($data) {
            return $this->success('删除成功');
        } else {
            return $this->error('删除失败');
        }
    }

    /*
     * 标题添加
     */
    public function add()
    {

        return $this->fetch();
    }

    public function add2()
    {
        $data = input('post.');
        $data3 = date("Y-m-d H:i:s");
        //dump($data);
        //exit();
        $list9 = $data['front_cover'];
        $list8 = $data['type'];
        $list1 = $data['title'];
        $list2 = $data['meet_time'];
        $list3 = $data['meet_endtime'];
        $list4 = $data['branch'];
        $list5 = $data['tel'];
        $list6 = $data['meet_home'];
        $list7 = $data['publisher'];
        if (empty($list9)) {
            return $this->error('会议封面不能为空!');
        } elseif (empty($list1)) {
            return $this->error('会议标题不能为空!');
        } elseif (empty($list2)) {
            return $this->error('会议开始时间不能为空');
        } elseif (empty($list3)) {
            return $this->error('会议结束时间不能为空');
        } elseif (empty($list6)) {
            return $this->error('会议地点不能为空');
        } elseif (empty($list4)) {
            return $this->error('主办方不能为空');
        } elseif (empty($list5)) {
            return $this->error('联系电话不能为空');
        } elseif (empty($list7)) {
            return $this->error('发布人不能为空');
        } else {
            $data1 = ['title' => $list1, 'meet_time' => $list2, 'meet_endtime' => $list3, 'branch' => $list4,
                'tel' => $list5, 'meet_home' => $list6, 'publisher' => $list7, 'create_time' => $data3, 'type' => $list8, 'front_cover' => $list9];
            $data2 = Db::table('pb_work')->insert($data1);
            if ($data2) {
                return $this->success('添加成功!', Url('Work/index'));
            } else {
                return $this->error('添加失败!');
            }
        }
    }

    //查看会议详情
    public function see($id)
    {
        $data = Db::table('pb_work')->where('id', $id)->find();
        if ($data['type'] == 1) {
            $list = array(
                'type' => '三会一课',
            );
        } else {
            $list = array(
                'type' => '志愿之家',
            );
        }
        //查找图片表里的图片
        $data2 = Db::table('pb_picture')->where('id', $data['front_cover'])->find();
        $data1 = Db::table('pb_wechat_user_tag')->where('tagid', $id)->select();
        $arr = array();
        //循环遍历三维数组$arr3
        foreach ($data1 as $value) {
            $arr[] = $value;
        }
        $this->assign('list', $list);
        $this->assign('arr', $arr);
        $this->assign('data', $data);
        $this->assign('data2', $data2);
        return $this->fetch();
    }

    //后台签到显示内容页面上的修改页面显示
    public function modifyxs($id)
    {
        $data = Db::table('pb_work')->where('id', $id)->find();
        //查找图片表里的图片
        $data2 = Db::table('pb_picture')->where('id', $data['front_cover'])->find();
        //dump($data);
        //exit();
        $this->assign('data2', $data2);
        $this->assign('data', $data);
        return $this->fetch();
    }

    //显示页面上的修改
    public function modify($id)
    {
        $data = input('post.');
        //dump($data);
        //exit();
        if (empty($data['front_cover'])) {
            if (empty($data['title'])) {
                return $this->error('会议标题不能为空!');
            } elseif (empty($data['meet_time'])) {
                return $this->error('会议开始时间不能为空!');
            } elseif (empty($data['meet_endtime'])) {
                return $this->error('会议结束时间不能为空!');
            } elseif (empty($data['meet_home'])) {
                return $this->error('会议地点不能为空!');
            } elseif (empty($data['branch'])) {
                return $this->error('主办方不能为空!');
            } elseif (empty($data['tel'])) {
                return $this->error('联系电话不能为空!');
            } elseif (empty($data['publisher'])) {
                return $this->error('发布人不能为空!');
            } else {
                $list = ['title' => $data['title'], 'meet_time' => $data['meet_time'], 'meet_endtime' => $data['meet_endtime'], 'branch' => $data['branch'], 'tel' => $data['tel'], 'meet_home' => $data['meet_home'], 'publisher' => $data['publisher']];
                $data2 = Db::table('pb_work')->where('id', $id)->update($list);
                if ($data2) {
                    return $this->success('修改成功!', Url('Work/see?id=' . $id));
                } else {
                    return $this->error('修改失败!');
                }
            }
        } else {
            if (empty($data['front_cover'])) {
                return $this->error('会议封面不能为空!');
            } elseif (empty($data['title'])) {
                return $this->error('会议标题不能为空!');
            } elseif (empty($data['meet_time'])) {
                return $this->error('会议开始时间不能为空!');
            } elseif (empty($data['meet_endtime'])) {
                return $this->error('会议结束时间不能为空!');
            } elseif (empty($data['meet_home'])) {
                return $this->error('会议地点不能为空!');
            } elseif (empty($data['branch'])) {
                return $this->error('主办方不能为空!');
            } elseif (empty($data['tel'])) {
                return $this->error('联系电话不能为空!');
            } elseif (empty($data['publisher'])) {
                return $this->error('发布人不能为空!');
            } else {
                $list = ['title' => $data['title'], 'meet_time' => $data['meet_time'], 'meet_endtime' => $data['meet_endtime'], 'branch' => $data['branch'], 'tel' => $data['tel'], 'meet_home' => $data['meet_home'], 'publisher' => $data['publisher'], 'front_cover' => $data['front_cover']];
                $data2 = Db::table('pb_work')->where('id', $id)->update($list);
                if ($data2) {
                    return $this->success('修改成功!', Url('Work/see?id=' . $id));
                } else {
                    return $this->error('修改失败!');
                }
            }
        }
    }
    
    
    
}