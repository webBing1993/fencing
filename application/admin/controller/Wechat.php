<?php
/**
 * Created by PhpStorm.
 * User: 虚空之翼 <183700295@qq.com>
 * Date: 16/5/20
 * Time: 09:14
 */

namespace app\admin\controller;


use app\admin\model\WechatDepartment;
use app\admin\model\WechatDepartmentUser;
use app\admin\model\WechatTag;
use app\admin\model\WechatUser;
use app\admin\model\WechatUserTag;
use com\wechat\QYWechat;
use wechat\TPWechat;
use think\Config;
use think\Input;

class Wechat extends Admin
{
    public function index() {

    }


    public function user() {
        $name = input('name');
        $map = ['name' => ['like', "%$name%"]];
        $list = $this->lists("WechatUser", $map);
        $department = WechatDepartment::column('id, name');

        foreach ($list as $key=>$value) {
            $departmentId = json_decode($value['department']);
            if($departmentId){
                foreach ($departmentId as $k=>$v) {
                    $name = $department[$v];
                    if ($k < count($departmentId) - 1 ) {
                        $name .= ',';
                    }
                }
                $list[$key]['department'] = $name;
            }else{
                $list[$key]['department'] = "暂无";
            }
        }
        // 状态转化
        wechat_status_to_string($list);
        $this->assign('list', $list);
        $this->assign('department', $department);

        return $this->fetch();
    }


    /**
     * 同步通讯录用户
     */
    public function synchronizeUser() {
        ini_set('max_execution_time', '60');
        $Wechat = new QYWechat(Config::get('party'));
        if($Wechat->errCode != 40001) {
            return $this->error("同步出错");
        }
        /* 同步部门 */
        $list = $Wechat->getDepartment();
        /* 同步最顶级部门下面的用户 */
        $num = 0;
        foreach ($list['department'] as $key=>$value) {
            $users = $Wechat->getUserListInfo($list['department'][$key]['id']);
            $num += count($users['userlist']);
            foreach ($users['userlist'] as $user) {
                $weObj = new TPWechat(config('weixinpay'));
                $toOpenId = $weObj->convertToOpenId(['userid' => $user['userid']]);
                if($toOpenId){
                    $user['openid'] = substr($toOpenId['openid'], 0, 15);
                }
                $user['department'] = json_encode($user['department']);
                $user['order'] = json_encode($user['order']);
                if(isset($user['extattr'])){
                    foreach ($user['extattr']['attrs'] as $value) {
                        switch ($value['name']){
                            case "出生日期":
                                $user['birthday'] = $value['value'];
//                                if(!empty($value['value'])) {
//                                    $user['age'] = date("Y",time()) - substr($value['value'],0,4);
//                                }else{
//                                    $user['age'] = null;
//                                }
                                break;
                            case "剑种":
                                $user['swords'] = $value['value'];
                                break;
                            case "就读学校":
                                $user['school'] = $value['value'];
                                break;
                            case "身份证号":
                                $user['identity'] = $value['value'];
                                break;
                            case "监护人":
                                $user['guardian_mobile'] = $value['value'];
                                break;
                            case "家庭住址":
                                $user['address'] = $value['value'];
                                break;
                            case "一级审批":
                                $user['telephone'] = $value['value'];
                                break;
                            case "二级审批":
                                $user['telephone2'] = $value['value'];
                                break;
                            case "抄送人1":
                                $user['push1'] = $value['value'];
                                break;
                            case "抄送人2":
                                $user['push2'] = $value['value'];
                                break;
                            case "抄送人3":
                                $user['push3'] = $value['value'];
                                break;
                            default:
                                break;
                        }
                    }
                    $user['extattr'] = json_encode($user['extattr']);
                }

                if(WechatUser::get(['userid'=>$user['userid']])) {
                    WechatUser::where(['userid'=>$user['userid']])->update($user);
                } else {
                    WechatUser::create($user);
                }
            }
        }
        $data = "用户数:".$num."!";
        return $this->success("同步成功", '', $data);
    }

    /**
     * 同步部门
     */
    public function synchronizeDp(){
        $Wechat = new QYWechat(Config::get('party'));
        if($Wechat->errCode != 40001) {
            return $this->error("同步出错");
        }
        /* 同步部门 */
        $list = $Wechat->getDepartment();

        foreach ($list['department'] as $key=>$value) {
            if(WechatDepartment::get($value['id'])){
                WechatDepartment::update($value);
            } else {
                WechatDepartment::create($value);
            }
        }

        /* 同步部门-用户关系表 */
        WechatDepartmentUser::where('1=1')->delete();
        foreach ($list['department'] as $key=>$value) {
            $users = $Wechat->getUserListInfo($value['id']);
            foreach ($users['userlist'] as $user) {
                $data = ['departmentid'=>$value['id'], 'userid'=>$user['userid']];
                if(empty(WechatDepartmentUser::where($data)->find())){
                    WechatDepartmentUser::create($data);
                }
                
//                if($value['id'] != 1) {
//                    $data1 = ['departmentid' => 1, 'userid' => $user['userid']];     //当部门补位1时补全用户
//                    if(empty(WechatDepartmentUser::where($data1)->find())){
//                        WechatDepartmentUser::create($data1);
//                    }
//                }
            }
        }

        $data = "同步部门数:".count($list['department'])."!";

        return $this->success("同步成功", '', $data);
    }

    /**
     * 同步标签
     */
    public function synchronizeTag(){
        $Wechat = new QYWechat(Config::get('party'));
        if($Wechat->errCode != 40001) {
            return $this->error("同步出错");
        }
        
        /* 同步标签 */
        WechatTag::where('1=1')->delete();
        $tags = $Wechat->getTagList();

        if (isset($tags['taglist'])){
            foreach ($tags['taglist'] as $tag) {
                if(WechatTag::get(['tagid'=>$tag['tagid']])) {
                    WechatTag::where(['tagid'=>$tag['tagid']])->update($tag);
                } else {
                    WechatTag::create($tag);
                }

            }
            /* 同步标签-用户关系表 */
            WechatUserTag::where('1=1')->delete();
            foreach ($tags['taglist'] as $value) {
                $users = $Wechat->getTag($value['tagid']);
                if(!empty($users['partylist'])){
                    foreach ($users['partylist'] as $user){
                        $info = $Wechat->getUserListInfo($user);
                        foreach ($info['userlist'] as $val){
                            $data = ['tagid' => $value['tagid'],'userid' => $val['userid']];
                            if(empty(WechatUserTag::where($data)->find())){
                                WechatUserTag::create($data);
                                if($value['tagid'] == 8){
                                    WechatUser::where(['userid'=>$user['userid']])->update(['train'=>1]);
                                }
                                if($value['tagid'] == 9){
                                    WechatUser::where(['userid'=>$user['userid']])->update(['vip'=>1]);
                                }
                                if(isset(WechatTag::TAG_ARRAY[$value['tagid']])){
                                    $member_type = WechatTag::TAG_ARRAY[$value['tagid']];
                                    if($member_type !== WechatUser::where(['userid'=>$user['userid']])->value('tag')){
                                        WechatUser::where(['userid'=>$user['userid']])->update(['tag'=>$member_type]);
                                    }
                                }
                            }
                        }
                    };
                }
                if(!empty($users['userlist'])){
                    foreach ($users['userlist'] as $user) {
                        $data = ['tagid'=>$value['tagid'], 'userid'=>$user['userid']];
                        if(empty(WechatUserTag::where($data)->find())){
                            WechatUserTag::create($data);
                            if($value['tagid'] == 8){
                                WechatUser::where(['userid'=>$user['userid']])->update(['train'=>1]);
                            }
                            if($value['tagid'] == 9){
                                WechatUser::where(['userid'=>$user['userid']])->update(['vip'=>1]);
                            }
                            if(isset(WechatTag::TAG_ARRAY[$value['tagid']])){
                                $member_type = WechatTag::TAG_ARRAY[$value['tagid']];
                                if($member_type !== WechatUser::where(['userid'=>$user['userid']])->value('tag')){
                                    WechatUser::where(['userid'=>$user['userid']])->update(['tag'=>$member_type]);
                                }
                            }
                        }
                    }
                }
            }
        }


        $data = "同步标签数:".count($tags['taglist'])."!";

        return $this->success("同步成功", '', $data);
    }
    
    public function department(){
        $list = $this->lists("WechatDepartment");
        $this->assign('list', $list);

        return $this->fetch();
    }

    public function tag(){
        $list = $this->lists("WechatTag");
        $this->assign('list', $list);

        return $this->fetch();
    }

    public function syncAll()
    {
        $this->synchronizeUser();
        $this->synchronizeDp();
        $this->synchronizeTag();

        return $this->success("一键同步成功");
    }


}