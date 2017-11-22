<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/14 0014
 * Time: 下午 1:04
 */

namespace app\home\controller;
use app\home\model\Apply;
use app\home\Model\WechatUser;
use app\home\Model\Work;
//签到
class Signin extends Base {
    /**
     * 签到主页
     */
    public function index(){

        return $this->fetch();
    }

    /**
     * 签到详情页
     */
    public function detail(){

        return $this->fetch();

    }
    /**
     * 签到功能
     */
    public function sign(){
        $id = input('id');
        $userid = input('user_id');
        $result = Apply::where(array('notice_id'=>$id,'userid'=>$userid))->find();
        if($result){
            // 已经签到
            $Wechat = WechatUser::where(['userid'=>$userid])->find();
            return array('status'=>0,'header'=>$Wechat['avatar'],'name'=>$Wechat['name']);
        }else{
            $Wechat = WechatUser::where(['userid'=>$userid])->find();
            if($Wechat){
                $data = array(
                    'sign_id' => $id,
                    'userid' =>$userid,
                    'score' => 0,
                    'status' =>0,
                    'create_time' => time()
                );
                $work = new Work();
                $Apply = new Apply();
                $type = $work->where('id',$id)->value('type');  // 1 三会一课  2 志愿之家
                if ($type == 1){
                    $res = $Apply->save($data);
                }else{
                    $mouth = date('m',time());
                    $score = $Apply->where(['userid' => $userid])->whereTime('create_time','d')->sum('score');
                    if ($score < 2){
                        WechatUser::where('userid',$userid)->setInc('volunteer_score');
                        $res = $Apply->save(['sign_id' => $id,'userid' => $userid,'score' => 1,'status' => 0,'mouth' => $mouth,'create_time' => time()]);
                    }else{
                        $res = $Apply->save(['sign_id' => $id,'userid' => $userid,'score' => 0,'mouth' => $mouth,'status' => 0,'create_time' => time()]);
                    }
                }
                if($res){
                    $Wechat = WechatUser::where(['userid'=>$userid])->find();
                    return array('status'=>1,'header'=>$Wechat['avatar'],'name'=>$Wechat['name']);
                }else{
                    return array('status'=>2,'header'=>null,'name'=>null);
                }
            }else{
                return array('status'=>2,'header'=>null,'name'=>null);
            }

        }
    }
}