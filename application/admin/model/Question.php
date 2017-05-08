<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/9/10
 * Time: 14:27
 */
namespace app\admin\model;
use think\Model;
class Question extends Model{
    protected $autoWriteTimestamp=true;
    public function Option(){

        //获取题目编号
        $questionId=$this->getData('id');
        $Option=new Option();
        //根据题目编号获取所有选项信息
        $option=$Option->where('question_id='.$questionId)->select();
        $arr=array();
        foreach($option as $value){
            if($value->content!==""){
                $arr[]=$value->style.'.&nbsp;'.$value->content;
            }
        }
        return $arr;
    }
    public function Right(){
        //获取正确答案
        $lists=array(
            1=>"A",
            2=>"B",
            3=>"C",
            4=>"D"
        );
        $rights=$this->getData('value');
        $arr=explode(":",$rights);
        foreach($arr as $value){
            $right[]=$lists[$value];
        }
        return $right;
    }
}