<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/8/6
 * Time: 14:36
 */

namespace app\home\controller;
use app\admin\model\WechatTag;
use app\home\model\ClassHour;
use app\home\model\ClassRecord;
use app\home\model\CourseUser;
use app\home\model\SignStatistics;
use app\home\model\Venue;
use app\home\model\WechatDepartmentUser;
use app\home\model\WechatUser;
use app\home\model\WechatUserTag;
use app\home\model\WorkRecord;
use app\home\model\Sign as SignModel;
use think\Controller;

class Sign extends Controller
{
    public function sindex()
    {
        $venue_id = input('venue_id');
//        $venue_id = 98;
        if (!$venue_id) {
            $this->error("参数错误！");
        }
        $this->assign('venue_id', $venue_id);
        $this->assign('token', $this->getToken());

        return $this->fetch();
    }

    public function sign()
    {
        $venue_id = input('venue_id');
        $openid = input('openid');
//        $venue_id = 98;
//        $openid = 'oKYU71ILqw-IVM1CrzkSZ4BOcSfM';

        $date = date('Y-m-d');
//        $date = "2018-08-01";
        $current_time = time();
//        $current_time = '1533109500';

        if (empty($venue_id)) {
            return $this->error("找不到匹配的场馆");
        }
        if (empty($openid)) {
            return $this->error("请重新扫描二维码");
        }
        if (strpos($openid, '//')) {
            return $this->error("请使用正确的二维码扫描");
        }
        if (strlen($openid) != 8) {
            return $this->error("请重试");
        }
        $msg = WechatUser::where(['openid' => $openid])->find();
        if (empty($msg)) {
            return $this->error("您还未录入系统，请联系管理员");
        }
        $userId = $msg['userid'];

        if (WechatUserTag::issetTag($userId, WechatTag::TAG_7)) {
            //工作人员上下班签到
            return $this->workSign($openid, $venue_id, $date, $current_time, $msg, 2);
        } else {
            if (WechatUserTag::issetTag($userId, WechatTag::TAG_3)) {
                //区域主管巡查签到
                return $this->moveSign($openid, $venue_id, $date, $current_time, $msg, 3);
            } else {
                //学员教练上课签到
                return $this->classSign($openid, $venue_id, $date, $current_time, $msg, 1);
            }
        }

    }

    //学员教练上课签到
    public function classSign($openid, $venue_id, $date, $current_time, $msg, $type)
    {
        $userId = $msg['userid'];
        $user_name = $msg['name'];
        $gender = $msg['gender'];
        $is_exist = ClassRecord::where(['openid' => $openid, 'venue_id' => $venue_id, 'date' => $date, 'status' => 0])->order('start_time')->select();
        if (!$is_exist)
        {
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
            $s = $current_time - $val['start_time'];
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
                    unset($is_exist[$key - 1]);
                }
            }
        }

        if (!$class) {
            $class = array_values($is_exist)[0];
        }

        $res = SignModel::where(['openid' => $openid, 'venue_id' => $venue_id, 'class_id' => $class['class_id'], 'type' => $type, 'mold' => 2])->find();

        $sign_in = SignModel::where(['openid' => $openid, 'venue_id' => $venue_id, 'class_id' => $class['class_id'], 'type' => $type, 'mold' => 1])->find();

        $used_num = 0;
        if ($res) {
            return $this->error($user_name . "已签退");
        } else if ($sign_in) {//签退
            $mold = 2;
            $tip = '签退';
            $real_time = strtotime(date('Y-m-d H:i:s', strtotime('-15 minute', $current_time)));

            // 签退时结算课时
            $classHourModel = ClassHour::get($class['class_id']);
            $num = $classHourModel['num'];
            $time = ($class['end_time'] - $class['start_time'])/$num;

            $j = 0;
            $k = 0;
            for ($i = 0; $i < $num; $i++) {
                $in_start = $class['start_time'] - 900 + ($time * $i);
                $in_end = $in_start + $time;
                $out_start = $class['start_time'] + 900 + ($time * $i);
                $out_end = $out_start + $time;
                if ($sign_in['create_time'] >= $in_start && $sign_in['create_time'] <= $in_end) {
                    $j = $i+1;
                }
                if ($current_time >= $out_start && $current_time <= $out_end) {
                    $k = $i+1;
                }
            }
            if ($k - $j >= 0) {
                $used_num = $k - $j + 1;
            } else {
                $used_num = 0;
            }

            if ($real_time < $class['start_time'] || $used_num == 0) {
                return $this->error($user_name . "已签到");
            }
        } else {//签到
            $mold = 1;
            $tip = '签到';
            $real_time = strtotime(date('Y-m-d H:i:s', strtotime('+15 minute', $current_time)));
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
            $comment_name = '就读学校';
            $comment = $msg['school'];
            $is_coach = SignModel::where(['venue_id' => $venue_id, 'class_id' => $class['class_id'], 'date' => $date, 'type' => $type, 'member_type' => 1, 'mold' => 1])->find();
            if (!$is_coach) {
                return $this->error("教练还未签到");
            }
        } else {//教练
            $member_type = 1;
            $member_name = '教练';
            $comment_name = '岗位级别';
            $comment = $msg['level'];
        }

        //存入签到表
        $model = SignModel::addSign($type, 'class_record', $class['id'], $userId, $user_name, $openid, $venue_id, $class['class_id'], $member_type, $mold, $date, $current_time);
        if ($model) {
            // 签退时结算课时
            if ($mold == 2) {
                //更新学员课程表的已用课时数
                CourseUser::where(['userid' => $userId, 'course_id' => $class['course_id']])->update(['used_num' => ['exp','`used_num`+'.$used_num], 'update_time' => $current_time]);
            }

            $response = [
                'id' => $model->id,
                'type' => $type,
                'name' => $user_name,
                'sex' => $gender == 2 ? '女' : '男',
                'comment_name' => $comment_name,
                'comment' => $comment,
                'swords' => $msg['swords'],
                'venue' => Venue::getName($venue_id),
                'tip' => '恭喜' . $member_name . $tip . '成功！',
            ];
            return $this->success($user_name . $tip, '', $response);
        } else {
            return $this->error($user_name . "请重新扫描二维码");
        }
    }

    //工作人员上下班签到
    public function workSign($openid, $venue_id, $date, $current_time, $msg, $type)
    {
        $userId = $msg['userid'];
        $user_name = $msg['name'];
        $gender = $msg['gender'];
        $is_exist = WorkRecord::where(['openid' => $openid, 'venue_id' => $venue_id, 'date' => $date, 'status' => 0])->find();
        if (!$is_exist) {
            return $this->error("您今日未排班，请联系管理员");
        }
        $sign_num = SignModel::where(['openid' => $openid, 'venue_id' => $venue_id, 'date' => $date, 'type' => $type])->count();

        if ($sign_num >= 2) {
            return $this->error($user_name . "已签退");
        } else if ($sign_num == 1) {//签退
            $mold = 2;
            $tip = '签退';
            $real_time = strtotime(date('Y-m-d H:i:s', strtotime('-15 minute', $current_time)));
            if ($real_time < $is_exist['start_time']) {
                return $this->error($user_name . "已签到");
            }
        } else {//签到
            $mold = 1;
            $tip = '签到';
        }
        //存入签到表
        $model = SignModel::addSign($type, 'work_record', $is_exist['id'], $userId, $user_name, $openid, $venue_id, 0, 0, $mold, $date, $current_time);
        if ($model) {
            $response = [
                'id' => $model->id,
                'type' => $type,
                'name' => $user_name,
                'sex' => $gender == 2 ? '女' : '男',
                'comment_name' => '岗位',
                'comment' => '员工',
                'tip' => '恭喜' . $tip . '成功！',
            ];
            return $this->success($user_name . $tip, '', $response);
        } else {
            return $this->error($user_name . "请重新扫描二维码");
        }
    }

    //区域主管巡查签到
    public function moveSign($openid, $venue_id, $date, $current_time, $msg, $type)
    {
        $userId = $msg['userid'];
        $user_name = $msg['name'];
        $gender = $msg['gender'];
        $sign_num = SignModel::where(['openid' => $openid, 'venue_id' => $venue_id, 'date' => $date, 'type' => $type])->count();

        if ($sign_num >= 2) {
            return $this->error($user_name . "已签退");
        } else if ($sign_num == 1) {//签退
            $mold = 2;
            $tip = '签退';
        } else {//签到
            $mold = 1;
            $tip = '签到';
        }
        //存入签到表
        $model = SignModel::addSign($type, '', 0, $userId, $user_name, $openid, $venue_id, 0, 0, $mold, $date, $current_time);
        if ($model) {
            $response = [
                'id' => $model->id,
                'type' => $type,
                'name' => $user_name,
                'sex' => $gender == 2 ? '女' : '男',
                'comment_name' => '岗位',
                'comment' => '区域主管',
                'tip' => '恭喜' . $tip . '成功！',
            ];
            return $this->success($user_name . $tip, '', $response);
        } else {
            return $this->error($user_name . "请重新扫描二维码");
        }
    }

    /**
     * 定时任务：每一分钟执行一次
     * 十五分钟自动签退
     */
    public function signOut()
    {
        $result = ClassHour::where(["FROM_UNIXTIME(end_time, '%Y-%m-%d')" => date('Y-m-d'), 'status' => 0])->select();
        $time = strtotime(date('Y-m-d H:i'));
        if ($result) {
            foreach ($result as $val) {
                $end_time = $val['end_time'] + 900;
                if ($end_time <= $time) {
                    $rs = SignModel::where(['class_id' => $val['id'], 'mold' => 1, 'type' => 1])->select();
                    if ($rs) {
                        foreach ($rs as $v) {
                            $res = SignModel::where(['class_id' => $val['id'], 'openid' => $v['openid'], 'mold' => 2, 'type' => 1])->find();
                            if (!$res) {
                                SignModel::addSign(1, 'class_record', $v['pid'], $v['userid'], $v['name'], $v['openid'], $v['venue_id'], $val['id'], $v['member_type'], 2, $v['date'], time());
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * 定时任务：每天下午6点执行一次
     * 前一天上课提醒
     * 会员过期提醒
     */
    public function setRemind()
    {

    }

    /**
     * 定时任务：每天凌晨2点执行一次
     * 统计学员教练的签到情况
     * 会员过期处理
     * 课程过期处理
     */
    public function setStatus()
    {
        /**
         *统计学员教练的签到情况
         */
        $date = date('Y-m-d', strtotime('-1 days'));
//        $date = '2018-08-27';
        //课程签到
        $rs = ClassRecord::where(['date' => $date, 'status' => 0])->select();
        if ($rs) {
            $this->getStatus($rs, $date, 'class_record', 1);
        }

        //上下班签到
        $rs2 = WorkRecord::where(['date' => $date, 'status' => 0])->select();
        if ($rs2) {
            $this->getStatus($rs2, $date, 'work_record', 2);
        }

        //巡查签到
        $rs3 = SignModel::where(['type' => 3, 'date' => $date, 'mold' => 1])->select();
        if ($rs3) {
            foreach ($rs3 as $val) {
                //存入签到统计表
                SignStatistics::add(3, '', 0, $val['userid'], $val['name'], $val['openid'], $val['venue_id'], 0, 1, $date);
            }
        }

    }

    public function getStatus($rs, $date, $table, $type){
        foreach ($rs as $val) {
            $res = SignModel::where(['type' => $type, 'pid' => $val['id'], 'date' => $date])->select();
            if (!$res) {//缺勤
                $status = 4;
            } else {
                $in = SignModel::where(['type' => $type, 'pid' => $val['id'], 'date' => $date, 'mold' => 1])->find();
                if (!$in) {
                    $status = 4;
                } else {
                    if ($in['create_time'] > $val['start_time']) {//迟到
                        $status = 2;
                    } else {
                        $out = SignModel::where(['type' => $type, 'pid' => $val['id'], 'date' => $date, 'mold' => 2])->find();
                        if (!$out) {
                            $status = 3;
                        } else {
                            if ($out['create_time'] < $val['end_time']) {//早退
                                $status = 3;
                            } else {
                                $status = 1;
                            }
                        }
                    }
                }
            }
            if ($type == 1) {
                $member_type = $val['member_type'];
            } else {
                $member_type = 0;
            }
            //存入签到统计表
            SignStatistics::add($type, $table, $val['id'], $val['userid'], $val['name'], $val['openid'], $val['venue_id'], $member_type, $status, $date);
        }
    }


    //语音播报接口获取token
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