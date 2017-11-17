<?php
/**
 * Created by PhpStorm.
 * User: Lxx<779219930@qq.com>
 * Date: 2017/1/4
 * Time: 10:15
 */

namespace app\admin\controller;
use app\admin\model\Redfilm;
use app\admin\model\Redbook;
use app\admin\model\Redmusic;
use app\admin\model\Redremark;

/**
 * Class Redcollection
 * @package app\admin\controller
 * 红色珍藏
 */
class Redcollection extends Admin {
    /**
     * 红色电影
     */
    public function film() {
        $map = array('status' => 1);
        $list = $this->lists("Redfilm",$map);
        int_to_string($list,array(
            'status' => array(0=>"未审核",1=>"已发布"),
        ));
        $this->assign('list',$list);
        
        return $this->fetch();
    } 
    
    /**
     * 电影新增
     */
    public function filmadd() {
        if(IS_POST) {
            $data = input('post.');
            unset($data['id']);
            if($data['recommend'] == 0) {
                unset($data['carousel_image']);
            }
            $data['create_user'] = $_SESSION['think']['user_auth']['id'];
            $filmModel = new Redfilm();
            $info = $filmModel->validate('Redfilm')->save($data);
            if($info) {
                return $this->success('添加成功',Url('Redcollection/film'));
            }else{
                return $this->error($filmModel->getError());
            }
        }else {
            $this->default_pic();
            $this->assign('msg','');
            return $this->fetch('filmedit');
        }
    }
    
    /**
     * 电影修改
     */
    public function filmedit() {
        if(IS_POST) {
            $data = input('post.');
            if($data['recommend'] == 0) {
                $data['carousel_image'] = null;
            }
            $data['update_time'] = time();
            $data['update_user'] = $_SESSION['think']['user_auth']['id'];
            $filmModel = new Redfilm();
            $info = $filmModel->validate('Redfilm')->save($data,['id'=>$data['id']]);
            if($info) {
                return $this->success('修改成功',Url('Redcollection/film'));
            }else{
                return $this->error($filmModel->getError());
            }
        }else {
            $this->default_pic();
            $filmModel = new Redfilm();
            $id = input('id');
            $msg = $filmModel->get($id);
            $this->assign('msg',$msg);
            return $this->fetch();
        }
    }
    
    /**
     * 电影删除
     */
    public function filmdel() {
        $id = input('id');
        $filmModel = new Redfilm();
        $map = array(
            'status' => -1,
        );
        $info = $filmModel->where('id',$id)->update($map);
        if($info) {
            return $this->success("删除成功");
        }else {
            return $this->error("删除失败");
        }
    }

    /**
     * 红色音乐
     */
    public function music() {
        $map = array(
            'status' => 1,
        );
        $list = $this->lists('Redmusic',$map);
        int_to_string($list,array(
            'status' => array(0=>"未审核",1=>"已发布"),
        ));
        $this->assign('list',$list);
        
        return $this->fetch();
    }
    
    /**
     * 音乐添加
     */
    public function musicadd() {
        if(IS_POST) {
            $data = input('post.');
            unset($data['id']);
            $data['create_user'] = $_SESSION['think']['user_auth']['id'];
            $musicModel = new Redmusic();
            $info = $musicModel->validate('Redmusic')->save($data);
            if($info) {
                return $this->success("添加成功",Url('Redcollection/music'));
            }else{
                return $this->error($musicModel->getError());
            }
        }else{
            $this->default_pic();
            $this->assign('msg','');
            return $this->fetch('musicedit');
        }
        
    }
    
    /**
     * 音乐修改
     */
    public function musicedit() {
        if(IS_POST) {
            $data = input('post.');
            $data['update_time'] = time();
            $data['update_user'] = $_SESSION['think']['user_auth']['id'];
            $musicModel = new Redmusic();
            $info = $musicModel->validate('Redmusic')->save($data,['id'=>$data['id']]);
            if($info) {
                return $this->success("修改成功",Url('Redcollection/music'));
            }else{
                return $this->error($musicModel->getError());
            }
        }else{
            $this->default_pic();
            $musicModel = new Redmusic();
            $id = input('id');
            $msg = $musicModel->get($id);
            $this->assign('msg',$msg);
            return $this->fetch();
        }
    }
    
    /**
     * 音乐删除
     */
    public function musicdel() {
        $id = input('id');
        $musicModel = new Redmusic();
        $map = array(
            'status' => -1,
        );
        $info = $musicModel->where('id',$id)->update($map);
        if($info) {
            return $this->success("删除成功");
        }else {
            return $this->error("删除失败");
        }
    }

    /**
     * 红色文学-书籍
     */
    public function book() {
        $map = array(
            'status' => 1,
        );
        $list = $this->lists('Redbook',$map);
        int_to_string($list,array(
            'status' => array(0=>"未审核",1=>"已发布"),
        ));
        $this->assign('list',$list);
        return $this->fetch();
    }

    /**
     * 文学新增
     */
    public function bookadd() {
        if(IS_POST) {
            $data = input('post.');
            unset($data['id']);
            $data['create_user'] = $_SESSION['think']['user_auth']['id'];
            $bookModel = new Redbook();
            $info = $bookModel->validate('Redbook')->save($data);
            if($info) {
                return $this->success("新增成功",Url('Redcollection/book'));
            }else{
                return $this->error($bookModel->getError());
            }
        }else {
            $this->default_pic();
            $this->assign('msg', '');
            return $this->fetch('bookedit');
        }
    }

    /**
     * 文学修改
     */
    public function bookedit() {
        if(IS_POST) {
            $data = input('post.');
            $data['update_time'] = time();
            $data['update_user'] = $_SESSION['think']['user_auth']['id'];
            $bookModel = new Redbook();
            $info = $bookModel->validate('Redbook')->save($data,['id'=>$data['id']]);
            if($info) {
                return $this->success("修改成功",Url('Redcollection/book'));
            }else{
                return $this->error($bookModel->getError());
            }
        }else{
            $this->default_pic();
            $id = input('id');
            $bookModel = new Redbook();
            $msg = $bookModel->get($id);
            $this->assign('msg',$msg);
            return $this->fetch();
        }
    }

    /**
     * 文学删除
     */
    public function bookdel() {
        $id = input('id');
        $bookModel = new Redbook();
        $map = array(
            'status' => -1,
        );
        $info = $bookModel->where('id',$id)->update($map);
        if($info) {
            return $this->success("删除成功");
        }else {
            return $this->error("删除失败");
        }
    }
}