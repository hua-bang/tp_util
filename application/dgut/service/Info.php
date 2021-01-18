<?php


namespace app\dgut\service;


use QL\QueryList;

class Info
{
    private $ql;
    private $url = "http://jwyd.dgut.edu.cn/frame/droplist/getDropLists.action";

    public function __construct()
    {
        $this->ql = QueryList::getInstance();
    }

    /**
     * Notes: 根据年级查询学院
     * Author: hua-bang
     * Date: 2021/1/19
     * Time: 4:08
     * @param $grade int 年级 例如:2018
     * @return mixed 学院列表
     */
    public function getInstituteByGrade($grade){
        $res = $this->ql->post($this->url,[
            'comboBoxName' => 'MsYXB',
            'paramValue' => 'nj='.$grade,
            'isYXB' => 0,
            'isCDDW' => 0,
            'isXQ' => 0,
            'isDJKSLB' => 0
        ])->getHtml();
        return json_decode($res,true);
    }

    /**
     * Notes: 根据年级和学院id查询专业
     * Author: hua-bang
     * Date: 2021/1/19
     * Time: 4:09
     * @param $grade int  年级 例如:2018
     * @param $instituteId string 学院id,根据上方获取学院接口中有获得 例如:41
     * @return mixed 专业数组
     */
    public function getMajorByGradeAndInstitute($grade, $instituteId){
        $res = $this->ql->post($this->url,[
            'comboBoxName' => 'MsYXB_Specialty',
            'paramValue' => 'nj='.$grade.'&dwh='.$instituteId,
            'isYXB' => 0,
            'isCDDW' => 0,
            'isXQ' => 0,
            'isDJKSLB' => 0
        ])->getHtml();
        return json_decode($res,true);
    }

    /**
     * Notes: 根据年级和学院id和专业id查询班级
     * Author: hua-bang
     * Date: 2021/1/19
     * Time: 4:11
     * @param $grade int 年级 例如:2018
     * @param $instituteId string 学院id,根据上方获取学院接口中有获得 例如:41
     * @param $majorId string 专业id,根据上方获取专业接口中有获得 例如:0404
     * @return mixed 班级数组
     */
    public function getClassByGradeAndInstituteAndMajor($grade, $instituteId,$majorId){
        $res = $this->ql->post($this->url,[
            'comboBoxName' => 'MsYXB_Specialty_Class',
            'paramValue' => 'nj='.$grade.'&dwh='.$instituteId."&zydm=".$majorId,
            'isYXB' => 0,
            'isCDDW' => 0,
            'isXQ' => 0,
            'isDJKSLB' => 0
        ])->getHtml();
        return json_decode($res,true);
    }
}