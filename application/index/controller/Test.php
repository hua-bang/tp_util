<?php


namespace app\index\controller;

use app\dgut\service\Lesson;
use app\utils\controller\Validate;
use app\utils\controller\VCode;
use think\Db;
use think\facade\Cache;

class Test
{

    public function info(){
        phpinfo();
    }

    public function testRedisCache($token){
        dump(Cache::get($token));
    }

    public function getLesson(){
        $grade = input("post.grade");
        $instituteId = input("post.instituteId");
        $majorId = input("post.majorId");
        $classId = input("post.classId");
        $termOrder = input("post.termOrder");
        $beginYear = input("post.termYear");
        $term = [
            "termOrder" => $termOrder,
            "beginYear" => $beginYear
        ];
        return (new Lesson())->getLessonTableFromJWXT($grade,$instituteId,$majorId,$classId,$term);
    }

}