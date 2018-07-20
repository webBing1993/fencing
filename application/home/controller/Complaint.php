<?php
/**
 * Created by PhpStorm.
 * User: 王泽锋
 * Date: 2018/07/20
 * Time: 14:38
 */

namespace app\home\controller;

use app\home\model\Comp;

class Complaint extends Base
{
    //教练投诉首页
    public function index(){

      return $this->fetch();
    }

    //教练投诉  添加
    public function add(){
        $data = input('post.');
        if(!empty($data['images'])){
            $data['front_cover'] = json_encode($data['images']);

        }else{
            $data['front_cover'] = '';
        }
        unset($data['images']);
        $CompModel = new Comp();
        $info = $CompModel->create($data);

        if($info) {
            return $this->success("提交成功");
        }else{
            return $this->error("提交失败");
        }
    }


}