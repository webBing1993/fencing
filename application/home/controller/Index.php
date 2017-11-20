<?php
/**
 * Created by PhpStorm.
 * User: Lxx<779219930@qq.com>
 * Date: 2016/4/20
 * Time: 13:47
 */

namespace app\home\controller;
use app\home\model\Study;
use app\home\model\News;
use app\home\model\Companys;
use app\home\model\Picture;
/**
 * 党建主页
 */
class Index extends Base {
    public function index(){
        $this->anonymous();
        $len = array('news' => 0,'study' => 0,'volunteer' => 0);
        $list = $this ->getDataList($len);
        $this ->assign('list',$list['data']);
        return $this->fetch();
    }

    /**
     * 获取数据列表 箬横动态  news  两学一做  study  志愿风采展  companys
     * @param $len
     */
    public function getDataList($len)
    {
        //从第几条开始取数据
        $count1 = $len['news'];   // 箬横动态
        $count2 = $len['study'];  // 两学一做
        $count3 = $len['volunteer'];  //志愿风采展
        $news = new News();
        $study = new Study();
        $companys = new Companys();
        $news_check = false; // 数据状态 true为取空
        $study_check = false;
        $companys_check = false;
        $all_list = array();
        //获取数据  取满12条 或者取不出数据退出循环
        while(true)
        {
            // 箬横动态
            if (!$news_check && count($all_list) < 12){
                $res1 = $news->getDataList($count1);
                if (empty($res1)){
                    $news_check = true;
                }else{
                    $count1 ++ ;
                    $all_list = $this->changeTpye($all_list,$res1,1);
                }
            }
            // 两学一做
            if(!$study_check &&
                count($all_list) < 12)
            {
                $res2 = $study ->getDataList($count2);
                if(empty($res2))
                {
                    $study_check = true;
                }else {
                    $count2 ++;
                    $all_list = $this ->changeTpye($all_list,$res2,2);
                }
            }
            // 志愿风采展
            if(!$companys_check &&
                count($all_list) < 12)
            {
                $res3 = $companys ->getDataList($count3);
                if(empty($res3))
                {
                    $companys_check = true;
                }else {
                    $count3 ++;
                    $all_list = $this ->changeTpye($all_list,$res3,3);
                }
            }
            if(count($all_list) >= 12 || ($news_check && $study_check && $companys_check))
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
     * @param $type 1 箬横动态  2 两学一做  3 志愿风采展
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
        $this ->anonymous();
        $len = input('post.');
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