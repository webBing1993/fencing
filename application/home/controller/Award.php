<?php
/**
 * Created by PhpStorm.
 * User: laowang
 * Date: 2017/12/18
 * Time: 上午9:55
 */

namespace app\home\controller;

use think\Controller;
use app\admin\model\Question;
class Award extends Controller
{
    /**
     * 首页
     */
    public function index(){
        //随机获取单选的题目
        $arr=Question::all(['type'=>0]);//查询全部单选
        foreach($arr as $value){
            $ids[]=$value->id;
        }
        $num=6;//题目数目
        $question=array();
        while(true) {
            if (count($question) == $num) {//数量检查
                break;
            } else {
                $index = mt_rand(0, count($ids) - 1);//随机值
                $question[] = $arr[$index];//重组数据
            }
        }
        
        $this->assign('question',$question);
        return $this->fetch();
    }
    
    /**
     * 答题页面
     */
    public function answer()
    {
        
        return $this->fetch();
    }
}