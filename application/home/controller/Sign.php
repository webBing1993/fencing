<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/6
 * Time: 14:36
 */

namespace app\home\controller;
use app\admin\model\WechatTag;
use app\home\model\ClassRecord;
use app\home\model\Venue;
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
        $venue_id = 98;
        $this->assign('venue_id',$venue_id);
        $this->assign('token',$this->getToken());

        return $this->fetch();
    }
    public function sign(){
//        $venue_id = input('venue_id');
//        $openid = input('openid');
        $venue_id = 98;
        $openid = 'oKYU71ILqw-IVM1CrzkSZ4BOcSfM';

//        var_dump(strlen($openid));die;
//        $date = date('Y-m-d');
        $date = "2018-08-01";
//        $current_time = time();
        $current_time = '1533128460';

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
                $tip = '签退';
                $real_time = strtotime(date('Y-m-d H:i:s', strtotime('-15 minute')));
                if ($real_time < $is_exist['start_time']) {
                    return $this->error($user_name."已签到");
                }
            } else {//签到
                $data['mold'] = 1;
                $tip = '签到';
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
                    'tip' => '恭喜'.$tip.'成功！',
                ];
                return $this->success($user_name.$tip, '', $response);
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
                    $tip = '签退';
                } else {//签到
                    $data['mold'] = 1;
                    $tip = '签到';
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
                        'comment' => '区域主管',
                        'tip' => '恭喜'.$tip.'成功！',
                    ];
                    return $this->success($user_name.$tip, '', $response);
                } else {
                    return $this->error($user_name."请重新扫描二维码");
                }
            } else {
                //学员教练上课签到
                $type = 1;
                $is_exist = ClassRecord::where(['openid' => $openid, 'venue_id' => $venue_id, 'date' => $date, 'status' => 0])->order('start_time')->select();
                if (!$is_exist) {
                    return $this->error("您今日没有课程，请联系管理员");
                }
                $t = 0;
                $class = [];
                $num = count($is_exist);
                foreach ($is_exist as $key => $val) {
                    if ($val['start_time'] <= $current_time && $val['end_time'] >= $current_time) {
                        $class = $val;//匹配正好在课程时间段内的课程
                    }
                    //获取不在课程时间段内的离开始时间最近的课程
                    $s = $current_time-$val['start_time'];
                    if ($s < 0) {
                        $s = -$s;
                    }
                    if ($key == 0) {
                        $t = $s;
                    } else {
                        if ($s >= $t) {
                            unset($is_exist[$key]);
                        } else {
                            $t = $s;
                            unset($is_exist[$key-1]);
                        }
                    }
                }

                if (!$class) {
                    $class = array_values($is_exist)[0];
                }

                $sign_num = SignModel::where(['openid' => $openid, 'venue_id' => $venue_id, 'date' => $date, 'type' => $type])->count();

                if ($sign_num >= 2*$num) {
                    return $this->error($user_name."已签退");
                } else if ($sign_num%2!=0) {//奇数=>签退
                    $data['mold'] = 2;
                    $tip = '签退';
                    $real_time = strtotime(date('Y-m-d H:i:s', strtotime('-15 minute')));
                    if ($real_time < $class['start_time']) {
                        return $this->error($user_name."已签到");
                    }
                } else {//偶数=>签到
                    $data['mold'] = 1;
                    $tip = '签到';
                    $real_time = strtotime(date('Y-m-d H:i:s', strtotime('+15 minute')));
                    if ($real_time < $class['start_time']) {
                        return $this->error("未到签到时间");
                    }
                    if ($real_time > $class['end_time']) {
                        return $this->error("已过签到时间");
                    }
                }
                //教练签到后学员才能签到
                if (WechatUserTag::issetTag($userId, WechatTag::TAG_8)) {//在训学员
                    $member_type = 2;
                    $member_name = '同学';
                    $comment = $msg['school'];
                    $is_coach = SignModel::where(['venue_id' => $venue_id, 'class_id' => $class['class_id'], 'date' => $date, 'type' => $type, 'member_type' => 1, 'mold' => 1])->find();
                    if (!$is_coach) {
                        return $this->error("教练还未签到");
                    }
                } else {//教练
                    $member_type = 1;
                    $member_name = '教练';
                    $comment = $msg['level'];
                }

                //存入签到表
                $data['type'] = $type;
                $data['table'] = 'class_record';
                $data['pid'] = $class['id'];
                $data['userid'] = $userId;
                $data['name'] = $user_name;
                $data['openid'] = $openid;
                $data['venue_id'] = $venue_id;
                $data['class_id'] = $class['class_id'];
                $data['member_type'] = $member_type;
                $data['date'] = $date;
                $data['create_time'] = $current_time;
                if ($model = SignModel::create($data)) {
                    // 签退时结算课时

                    $response = [
                        'id' => $model->id,
                        'type' => $type,
                        'name' => $user_name,
                        'sex' => $msg['gender']==2?'女':'男',
                        'comment' => $comment,
                        'swords' => $msg['swords'],
                        'venue' => Venue::getName($venue_id),
                        'tip' => '恭喜'.$member_name.$tip.'成功！',
                    ];
                    return $this->success($user_name.$tip, '', $response);
                } else {
                    return $this->error($user_name."请重新扫描二维码");
                }
            }
        }


    }

    public function getToken()
    {
        define('DEMO_CURL_VERBOSE', false);

        # 填写网页上申请的appkey 如 $apiKey="g8eBUMSokVB1BHGmgxxxxxx"
        $apiKey = "DMG0S55sDWgy5bwQKK02TpI8";
        # 填写网页上申请的APP SECRET 如 $secretKey="94dc99566550d87f8fa8ece112xxxxx"
        $secretKey = "d1ff25db291db83035f2b431b8e1adcf";

        /** 公共模块获取token开始 */

        $auth_url = "https://openapi.baidu.com/oauth/2.0/token?grant_type=client_credentials&client_id=" . $apiKey . "&client_secret=" . $secretKey;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $auth_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); //信任任何证书
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0); // 检查证书中是否设置域名,0不验证
        curl_setopt($ch, CURLOPT_VERBOSE, DEMO_CURL_VERBOSE);
        $res = curl_exec($ch);
        if (curl_errno($ch)) {
            print curl_error($ch);
        }
        curl_close($ch);

//        echo "Token URL response is " . $res . "\n";
        $response = json_decode($res, true);

        if (!isset($response['access_token'])) {
            echo "ERROR TO OBTAIN TOKEN\n";
            exit(1);
        }
        if (!isset($response['scope'])) {
            echo "ERROR TO OBTAIN scopes\n";
            exit(2);
        }

        if (!in_array('audio_tts_post', explode(" ", $response['scope']))) {
            echo "DO NOT have tts permission\n";
            // 请至网页上应用内开通语音合成权限
            exit(3);
        }

        $token = $response['access_token'];

        return $token;
    }

    public function signS(){
        return $this->fetch();
    }

}