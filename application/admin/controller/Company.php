<?php
/**
 * Created by PhpStorm.
 * User: 老王
 * Date: 2017/5/11
 * Time: 14:45
 */
namespace app\admin\controller;

use app\admin\model\Push;
use app\admin\model\WechatDepartment;
use com\wechat\TPQYWechat;
use app\admin\model\Picture;
use think\Config;
use app\admin\model\Company as CompanyModel;
use think\Url;
use think\Db;

/*
 * 志愿之家  控制器
 */

class Company extends Admin
{
    /*
     * 点亮微心愿   主页
     */
    public function index()
    {
        $map = array(
            'status' => array('egt', 0),
            'type' => '1',
        );
        $list = $this->lists('Company', $map);
        int_to_string($list, [
            'status' => [0 => "待审核", 1 => "已发布"],
        ]);
        $this->assign('list', $list);
        return $this->fetch('Company/recruit');
    }
    
    //志愿招募
    public function recruit()
    {
        $map = array(
            'status' => array('eq', 1),
            'type' => '2',
        );
        $list = $this->lists('Company', $map);
        int_to_string($list, array(
            'status' => array(0 => "待审核", 1 => "已发布"),
        ));
        //dump($list);
        //exit;
        $this->assign('list', $list);

        return $this->fetch();
    }

    //查看招募的用户
    public function recruitname($id){
        $data=Db::table('pb_company_recruit_receive')->where('rid',$id)->select();
        $list=Db::table('pb_company')->where('id',$id)->find();
        //dump($data);
        $this->assign('data',$data);
        $this->assign('list',$list);
        return $this->fetch();
    }

    //志愿招募添加
    public function recruitadd($type)
    {
        $this->assign('type',$type);
        if (IS_POST) {
            $data = input('post.');
            $result = $this->validate($data, 'Company');  // 验证  数据
            if (true !== $result) {
                return $this->error($result);
            } else {
                $companyModel = new CompanyModel();
                $data['create_user'] = $_SESSION['think']['user_auth']['id'];
                $data['start_time'] = strtotime($data['start_time']);
                $res = $companyModel->save($data);
                if ($res) {
                    if ($data['type'] == 1) {
                        return $this->success("添加微心愿成功!", Url('Company/index'));
                    } else {
                        return $this->success("添加志愿招募成功!", Url('Company/recruit'));
                    }
                } else {
                    return $this->error("添加失败!");
                }
            }
        } else {
            return $this->fetch();
        }
    }

    //删除
    public function recruitdel($id)
    {
        $data = Db::table('pb_company')->where('id', $id)->update(['status' => '-1']);
        if ($data) {
            return $this->success('删除成功！', Url('Company/recruit'));
        } else {
            return $this->error('删除失败!');
        }
    }

    //修改
    public function recruitedit($id)
    {
        $data = Db::table('pb_company')->where('id', $id)->find();
        $data2 = Db::table('pb_picture')->where('id', $data['image'])->find();
        $this->assign('data', $data);
        $this->assign('data2', $data2);
        if (IS_POST) {
            $data1 = input('post.');
            //dump($data);
            //exit();
            $result = $this->validate($data1, 'Company');  // 验证  数据
            if (true !== $result) {
                return $this->error($result);
            } else {
                $companyModel = new CompanyModel();
                $data1['create_user'] = $_SESSION['think']['user_auth']['id'];
                $data1['start_time'] = strtotime($data1['start_time']);
                $res = $companyModel->save($data1, ['id' => $id]);
                if ($res) {
                    if ($data['type']==2) {
                        return $this->success("修改志愿招募成功!", Url('Company/recruit'));
                    }else{
                        return $this->success("修改微心愿成功!", Url('Company/index'));
                    }
                    } else {
                    return $this->error("修改失败!");
                }
            }
        } else {
            return $this->fetch();
        }
    }

    //志愿风采展
    public function show($id){
        $map = array(
            'status' => array('eq', 0),
            'type'=>array('eq',$id),
        );
        $list = $this->lists('Companys', $map);
        int_to_string($list, array(
            'status' => array( 0 => "已发布"),
        ));
       
        $this->assign('list', $list);
        $this->assign('id',$id);
        
        return $this->fetch();
    }

    //添加
    public function showadd($type)
    {
        $this->assign('type',$type);
        if (IS_POST) {
            $data = input('post.');
            //dump($data);
            //exit();
            $result = $this->validate($data, 'Companys');  // 验证  数据
            if (true !== $result) {
                return $this->error($result);
            } else {
                $data['create_user'] = $_SESSION['think']['user_auth']['id'];
                $res=Db::table('pb_companys')->insert($data);
                if ($res) {
                        return $this->success("添加风采成功!",Url('Company/show?id='.$type));
                } else {
                    return $this->error("添加失败!");
                }
            }
        } else {
            return $this->fetch();
        }
    }

    //修改
    public function showedit($id){
        $list=Db::table('pb_companys')->where('id',$id)->find();
        $data = Db::table('pb_picture')->where('id', $list['front_cover'])->find();

        $this->assign('list',$list);
        $this->assign('data',$data);
        if (IS_POST) {
            $data1 = input('post.');
            //dump($data);
            //exit();
            $result = $this->validate($data1, 'Companys');  // 验证  数据
            if (true !== $result) {
                return $this->error($result);
            } else {

                $data1['create_user'] = $_SESSION['think']['user_auth']['id'];

                $res = Db::table('pb_companys')->where('id',$id)->update($data1);
                if ($res) {
                        return $this->success("修改风采展示成功!",Url('Company/show?id='.$data1['type']));
                } else {
                    return $this->error("修改失败!");
                }
            }
        } else {
            return $this->fetch();
        }
    }

    //风采展删除
    public function showdel($id){
        $data = Db::table('pb_companys')->where('id', $id)->update(['status' => '-1']);
        if ($data) {
            return $this->success('删除成功！', Url('Company/show'));
        } else {
            return $this->error('删除失败!');
        }
    }
    //风采展标题显示
    public function showbt(){
        $map = array(
            'status' => array('eq', 0),
        );
        $list = $this->lists('Companyst', $map);
        int_to_string($list, array(
            'status' => array( 0 => "已发布"),
        ));
        $this->assign('list', $list);
        
        return $this->fetch();
    }
    
    //添加
    public function showbtadd(){
        if (IS_POST) {
            $data = input('post.');
            $result = $this->validate($data, 'Companys');  // 验证  数据
            if (true !== $result) {
                return $this->error($result);
            } else {
                //$data['create_user'] = $_SESSION['think']['user_auth']['id'];
                $res=Db::table('pb_companyst')->insert($data);
                if ($res) {
                    return $this->success("添加风采成功!", Url('Company/showbt'));
                } else {
                    return $this->error("添加失败!");
                }
            }
        } else {
            return $this->fetch();
        }
    }
    //风采展修改
    public function showbtedit($id){
        $list=Db::table('pb_companyst')->where('id',$id)->find();
        $data = Db::table('pb_picture')->where('id', $list['front_cover'])->find();

        $this->assign('list',$list);
        $this->assign('data',$data);
        if (IS_POST) {
            $data1 = input('post.');
            //dump($data);
            //exit();
            $result = $this->validate($data1, 'Companys');  // 验证  数据
            if (true !== $result) {
                return $this->error($result);
            } else {
                $res = Db::table('pb_companyst')->where('id',$id)->update($data1);
                if ($res) {
                    return $this->success("修改风采展示成功!", Url('Company/showbt'));
                } else {
                    return $this->error("修改失败!");
                }
            }
        } else {
            return $this->fetch();
        }
    }

    //风采展标题删除
    public function showbtdel($id){
        $data = Db::table('pb_companyst')->where('id', $id)->update(['status' => '-1']);
        if ($data) {
            return $this->success('删除成功！', Url('Company/showbt'));
        } else {
            return $this->error('删除失败!');
        }


    }

}
