<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/6
 * Time: 14:36
 */

namespace app\home\controller;
use app\admin\model\WechatTag;
use app\home\model\WechatDepartmentUser;
use app\home\model\WechatUser;
use app\home\model\WechatUserTag;
use app\home\model\WorkRecord;
use app\home\model\Sign as SignModel;
use think\Controller;

class Sign extends Controller
{
    public function index(){
//        $venue_id = input('venue_id');
//        $openid = input('openid');
        $venue_id = 98;
        $openid = 'oKYU71ILqw-IVM1CrzkSZ4BOcSfM';

//        var_dump(strlen($openid));die;
        $date = date('Y-m-d');
        $current_time = time();

        if (empty($openid)) {
            return $this->error("请重新扫描二维码");
        }
        if (strlen($openid) != 28) {
            return $this->error("请使用正确的二维码扫描");
        }
        $msg = WechatUser::where(['openid' => $openid])->find();
        if (empty($msg)) {
            return $this->error("您还未录入系统，请联系管理员");
        }
        $userId = $msg['userid'];
        $user_name = $msg['name'];

        if (WechatUserTag::issetTag($userId, WechatTag::TAG_7)) {
            //工作人员上下班签到
            $type = 2;
            $is_exist = WorkRecord::where(['openid' => $openid, 'venue_id' => $venue_id, 'date' => $date, 'status' => 0])->find();
            if (!$is_exist) {
                return $this->error("您今日未排班，请联系管理员");
            }
            $sign_num = SignModel::where(['openid' => $openid, 'venue_id' => $venue_id, 'date' => $date, 'type' => $type])->count();

            if ($sign_num >= 2) {
                return $this->error($user_name."已签退");
            } else if ($sign_num == 1) {//签退
                $data['mold'] = 2;
                $real_time = strtotime(date('Y-m-d H:i:s', strtotime('-15 minute')));
                if ($real_time < $is_exist['start_time']) {
                    return $this->error($user_name."已签到");
                }
            } else {//签到
                $data['mold'] = 1;
            }
            //存入签到表
            $data['type'] = $type;
            $data['table'] = 'work_record';
            $data['pid'] = $is_exist['id'];
            $data['userid'] = $userId;
            $data['name'] = $user_name;
            $data['openid'] = $openid;
            $data['venue_id'] = $venue_id;
            $data['date'] = $date;
            $data['create_time'] = $current_time;
            if ($model = SignModel::create($data)) {
                $response = [
                    'id' => $model->id,
                    'type' => $type,
                    'name' => $user_name,
                    'sex' => $msg['gender']==2?'女':'男',
                    'comment' => '员工',
                    'tip' => '恭喜签到成功！',
                ];
                return $this->success($user_name."签到成功", '', $response);
            } else {
                return $this->error($user_name."请重新扫描二维码");
            }
        } else {
            if (WechatUserTag::issetTag($userId, WechatTag::TAG_3)) {
                //区域主管巡查签到
                $type = 3;
                $sign_num = SignModel::where(['openid' => $openid, 'venue_id' => $venue_id, 'date' => $date, 'type' => $type])->count();

                if ($sign_num >= 2) {
                    return $this->error($user_name."已签退");
                } else if ($sign_num == 1) {//签退
                    $data['mold'] = 2;
                } else {//签到
                    $data['mold'] = 1;
                }
                //存入签到表
                $data['type'] = $type;
                $data['table'] = '';
                $data['pid'] = 0;
                $data['userid'] = $userId;
                $data['name'] = $user_name;
                $data['openid'] = $openid;
                $data['venue_id'] = $venue_id;
                $data['date'] = $date;
                $data['create_time'] = $current_time;
                if ($model = SignModel::create($data)) {
                    $response = [
                        'id' => $model->id,
                        'type' => $type,
                        'name' => $user_name,
                        'sex' => $msg['gender']==2?'女':'男',
                        'comment' => '员工',
                        'tip' => '恭喜签到成功！',
                    ];
                    return $this->success($user_name."签到成功", '', $response);
                } else {
                    return $this->error($user_name."请重新扫描二维码");
                }
            } else {
                //学员教练上课签到

            }
        }


        return $this->fetch();
    }
    public function signS(){
        return $this->fetch();
    }
}