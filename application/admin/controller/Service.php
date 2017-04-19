<?php
/**
 * Created by PhpStorm.
 * User: 虚空之翼 <183700295@qq.com>
 * Date: 16/3/25
 * Time: 09:12
 * 微信事件接口
 */
namespace app\admin\controller;

use app\admin\model\WechatLog;
use com\wechat\QYWechat;
use com\wechat\TPQYWechat;
use com\wechat\TPWechat;
use think\Config;
use think\Controller;
use think\Log;

class Service extends Controller
{

    // 服务号接收的应用
    public function event($c) {
        $Wechat = new TPQYWechat(Config::get('party'));
        $Wechat->valid();

        $type = $Wechat->getRev()->getRevType();
        switch ($type) {
            case QYWechat::MSGTYPE_TEXT:
                $Wechat->text("您好！感谢关注！")->reply();
                break;
            case QYWechat::MSGTYPE_EVENT:
                $event = $Wechat->getRev()->getRevEvent();
                switch ($event['event']) {
                    case 'subscribe':
                        switch($c){
                            case 0: //  新手指南
                                $newsData = array(
                                    '0'=> array(
                                        'Title' => "欢迎您关注“德清地信小镇党建”",
                                        'Description' => "内含企业二维码，可转发给同事关注",
                                        'PicUrl' => "http://dqpb.0571ztnet.com/home/images/default_avatar.jpg",
                                        'Url' => "http://x.eqxiu.com/s/9DeclTiO",
                                    ),
                                );
                                $Wechat->news($newsData)->reply();
                                break;
                            case 1: // 小镇动态
                                $replyText = "您好！欢迎关注小镇动态！您可以查看时政新闻、党建要闻等，同时线上互动评论。还可以直接查看小镇动态订阅号。";
                                $Wechat->text($replyText)->reply();
                                break;
                            case 2: // 支部活动
                                $replyText = "您好！欢迎关注支部活动！您可以查看各种通知、会议及情况等，同时线上互动评论。";
                                $Wechat->text($replyText)->reply();
                                break;
                            case 3: // 两学一做
                                $replyText = "您好！欢迎关注两学一做！一起回顾党史，在线答题，参与专题讨论，查看党性体捡情况。";
                                $Wechat->text($replyText)->reply();
                                break;
                            case 4: // 地信红盟
                                $replyText = "您好！欢迎关注地信红盟！“一周一轮值，一月一主题，一季一交流”，每季每月每周发布活动。";
                                $Wechat->text($replyText)->reply();
                                break;
                            case 5: // 红领学院
                                $replyText = "您好！欢迎加入红领学院！各位导师课程开始啦，一起去看看党团活动、论坛通知，参与论坛讨论吧。";
                                $Wechat->text($replyText)->reply();
                                break;
                            case 6: //志愿服务
                                $replyText = "您好！欢迎您加入志愿服务！查看并评论服务团队新闻，认领微心愿查看领取情况。如果您是管理员，还可以直接发布志愿哦！";
                                $Wechat->text($replyText)->reply();
                                break;
                            case 7: // 人才服务
                                $replyText = "您好！人才服务等候您多时了！我们提供各类人才政策、申报流程、优待政策，并为您提供创业典范。愿您成为下一个典范。";
                                $Wechat->text($replyText)->reply();
                                break;
                            case 8: //通讯名录
                                $replyText = "您好！您的专属通讯名录诞生啦！赶紧查看通讯录小伙伴吧！但仅可见本部门及子部门下的小伙伴哦。";
                                $Wechat->text($replyText)->reply();
                                break;
                            case 9: // 个人中心
                                $replyText = "您好！欢迎进入个人中心！查看个人信息及排行榜，展示二维码，查看积分并兑换好礼。";
                                $Wechat->text($replyText)->reply();
                                break;
                            case 10: // 消息审核
                                $replyText = "您好！欢迎进入消息审核！您可以直接在手机端对党员发布的通知、笔记、意见、反馈等审核，审核通过直接发布。";
                                $Wechat->text($replyText)->reply();
                                break;
                        }
                        break;
                    case 'enter_agent':
                        $data = array(
                            'event' => $event['event'],
                            'msgtype' => $type,
                            'agentid' => $Wechat->getRev()->getRevAgentID(),
                            'create_time' => $Wechat->getRev()->getRevCtime(),
                            'event_key' => isset($event['key']) ? $event['key'] : '',
                            'userid' => $Wechat->getRev()->getRevFrom()
                        );
//                        Log::record("进入事件：".json_encode($data));
                        $id  = WechatLog::create($data);
//                        Log::record("创建记录：".$id);
                        //$Wechat->text(json_encode($data))->reply();
                        save_log($Wechat->getRev()->getRevFrom(), 'WechatLog');
                        break;
                }
                break;
            case QYWechat::MSGTYPE_IMAGE:
                break;
            default:
                $Wechat->text("您好！感谢关注！")->reply();
        }

    }

    // 企业号验证
    public function oauth() {
        $weObj = new TPQYWechat(Config::get('party'));
        $weObj->valid();
    }

    //订阅号验证
    public function oauth2(){
        $Wechat = new TPWechat(Config::get('news'));
        $Wechat->valid();
    }

    // 创建订阅号菜单
    public function menu() {
        $menu["button"] = array(
            array(
                "type"=>"view",
                "name"=>"第一聚焦",
                "url"=>"http://party.0571ztnet.com/home/focus/index"
            ),
            array(
                "type"=>"view",
                "name"=>"活动通知",
                "url"=>"http://party.0571ztnet.com/home/activity/index"
            ),
            array(
                "type"=>"view",
                "name"=>"品牌特色",
                "url"=>"http://party.0571ztnet.com/home/special/index"
            ),
        );

        $Wechat = new TPWechat(Config::get('news'));
        $result = $Wechat->createMenu($menu);

        if($result) {
            return $this->success('提交成功');
        } else {
            return $this->error('错误代码：'.$result['errcode'].'，消息：'.$result['errmsg']);
        }
    }

    public function media() {
        $Wechat = new TPWechat(Config::get('news'));
        $list = $Wechat->getForeverList("news", 0, 20);
        dump($list);
    }
}