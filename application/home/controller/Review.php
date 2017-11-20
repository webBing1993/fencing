<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/16 0016
 * Time: 上午 9:47
 */

namespace app\home\controller;
use app\home\model\Notice;
use app\home\model\Company;
use app\home\model\Picture;
/**
 * Class Review
 * @package app\home\controller  消息审核
 */
class Review extends Base{
    /**
     * @return mixed 主页
     */
    public function index(){
        $this ->anonymous();
        $len = array('meeting' => 0,'dream' => 0,'volunteer' => 0);
        $list = $this ->getDataList($len);
        $this ->assign('list',$list['data']);
        return $this->fetch();
    }
    /**
     * 获取数据列表 会议纪要 notice  微心愿 company type 1 志愿招募 company type 2
     * @param $len
     */
    public function getDataList($len)
    {
        //从第几条开始取数据
        $count1 = $len['meeting'];   // 会议纪要
        $count2 = $len['dream'];  // 微心愿
        $count3 = $len['volunteer'];  //志愿招募
        $notice = new Notice();
        $company = new Company();
        $notice_check = false; // 数据状态 true为取空
        $company1_check = false;
        $company2_check = false;
        $all_list = array();
        //获取数据  取满12条 或者取不出数据退出循环
        while(true)
        {
            // 会议纪要
            if (!$notice_check && count($all_list) < 12){
                $res1 = $notice->getDataList($count1);
                if (empty($res1)){
                    $notice_check = true;
                }else{
                    $count1 ++ ;
                    $all_list = $this->changeTpye($all_list,$res1,1);
                }
            }
            // 微心愿
            if(!$company1_check &&
                count($all_list) < 12)
            {
                $res2 = $company ->getDataList($count2,1);
                if(empty($res2))
                {
                    $company1_check = true;
                }else {
                    $count2 ++;
                    $all_list = $this ->changeTpye($all_list,$res2,2);
                }
            }
            // 志愿招募
            if(!$company2_check &&
                count($all_list) < 12)
            {
                $res3 = $company ->getDataList($count3,2);
                if(empty($res3))
                {
                    $company2_check = true;
                }else {
                    $count3 ++;
                    $all_list = $this ->changeTpye($all_list,$res3,3);
                }
            }
            if(count($all_list) >= 12 || ($notice_check && $company1_check && $company2_check))
            {
                break;
            }
        }
        if (count($all_list) != 0)
        {
            return ['code' => 1,'msg' => '获取成功','data' => $all_list];
        }else{
            return ['code' => 0,'msg' => '获取失败','data' => $all_list];
        }
    }

    /**
     * 进行数据区分
     * @param $list
     * @param $type 1 会议纪要  2 微心愿 3 志愿招募
     */
    private function changeTpye($all,$list,$type){
        $list['class'] = $type;
        array_push($all,$list);
        return $all;
    }
    /**
     * 首页加载更多新闻列表
     * @return array
     */
    public function moreDataList(){
        $len = input('get.');
        $list = $this ->getDataList($len);
        //转化图片路径 时间戳
        foreach ($list['data'] as $k => $v)
        {
            $img_path = Picture::get($list['data'][$k]['front_cover']);
            $list['data'][$k]['time'] = date('Y-m-d',$v['create_time']);
            $list['data'][$k]['path'] = $img_path['path'];
        }
        return $list;
    }
}