<?php


namespace app\dgut\controller;
use app\dgut\service\Lesson as LessonService;

class Lesson
{
    protected $url = "http://jwyd.dgut.edu.cn/public/dykb.bjkb_data.jsp";
    protected $beginYear;   //第一个学年
    protected $endYear; //第二个学年
    protected $term;
    protected $week = ["一","二","三","四","五","六","七"];
    protected $currentWeek;
    protected $timeArray = ["0:00","9:15","10:10","11:10","12:00","15:15","16:10","17:10","18:05","20:15","21:10","22:00"];
    const AUTUMN_TERM_MONTH = 8;
    const SPRING_TERM_MONTH = 2;
    public function getTerm(){
        $year = date("Y");
        $month = date("m");
        if($month<self::AUTUMN_TERM_MONTH){
            $termOrder = $month<=self::SPRING_TERM_MONTH?1:2;
            $beginYear = $year - 1;
        }else{
            $termOrder = 1;
            $beginYear = $year;
        }
        return [
            "termOrder" => $termOrder,
            "beginYear" => $beginYear
        ];
    }

    public function getNowLesson($grade,$instituteId,$majorId,$classId){
        $term = $this->getTerm();
        return (new LessonService())->getLessonTableFromJWXT($grade,$instituteId,$majorId,$classId,$term);
    }
}