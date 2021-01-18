<?php


namespace app\dgut\controller;
use app\dgut\service\Info as InfoService;

class Info
{
    public function getInstitute($grade) {
        return (new InfoService())->getInstituteByGrade($grade);
    }

    public function getMajor($grade,$instituteId){
        return (new InfoService())->getMajorByGradeAndInstitute($grade,$instituteId);
    }

    public function getClass($grade, $instituteId,$majorId) {
        return (new InfoService())->getClassByGradeAndInstituteAndMajor($grade,$instituteId,$majorId);
    }
}