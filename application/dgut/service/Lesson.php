<?php


namespace app\dgut\service;


use app\lib\exception\TableException;
use app\utils\controller\Captcha;
use QL\QueryList;

class Lesson
{
    private $validateUrl = "http://jwyd.dgut.edu.cn/cas/genValidateCode";
    private $url = "http://jwyd.dgut.edu.cn/public/dykb.bjkb_data.jsp";
    protected $week = ["一","二","三","四","五","六","七"];

    public function getLessonTableFromJWXT($grade,$instituteId,$majorId,$classId,$term){
        $ql = QueryList::getInstance();
        $url = $this->url;
        $postData = $this->initPostData($grade,$instituteId,$majorId,$classId,$term);
        $firstRule = [
            'table' => ['table:eq(0)','html','',function($content){return iconv("gb2312","utf-8//IGNORE", $content);}],
            "tip" => ['table:eq(1)','text','',function($content){return iconv("gb2312","utf-8//IGNORE", $content);}]
        ];
        $weekArray = array_flip($this->week);
        $ql->post($url,$postData,[
            'timeout' => 100,
        ]);
        return $ql->rules($firstRule)->query()->getData(function($item) use ($term, $classId,$weekArray){
            if(!$item["table"]){
                throw new TableException();
            }
            $item['thread'] = (new QueryList)->html($item['table'])->rules([
                'key' => ['thead>tr>td','html']
            ])->query()->getData();
            $item['table'] = (new QueryList)->html($item['table'])->rules([
                'tr' => ["tbody>tr",'html']
            ])->query()->getData(function ($i) use ($term, $classId,$weekArray){
                $i['tr'] = (new QueryList)->html($i['tr'])->rules([
                    'className' => ["td:eq(0)","text","",function($content){
                        $pattern = '/^\[\d+\]/';
                        $content = preg_replace($pattern,"",$content,1);
                        return $content;
                    }],
                    'classRoom' => ['td:eq(-1)','text'],
                    'times' => ['td:eq(-2)','text'],
                    'day' => ['td:eq(-2)','text','',function($content) use ($weekArray){
                        $content = substr($content,0,3);
                        if($content)
                            $content = $weekArray[$content]+1;
                        else
                            $content = 0;
                        return $content;
                    }],
                    'beginTime' => ['td:eq(-2)','text','',function($content){
                        $pattern = '/\d+/';
                        if(preg_match_all($pattern, $content, $match)){
                            $content = $match[0][0];
                        }
                        return $content;
                    }],
                    'endTime' =>  ['td:eq(-2)','text','',function($content){
                        $pattern = '/\d+/';
                        if(preg_match_all($pattern, $content, $match)){
                            $content = $match[0][1];
                        }
                        return $content;
                    }],
                    'teacher'  => ['td:[style="text-align:left;width:55mm;"]','html'],
                    'credit' => ['td:eq(1)','text'],
                    'method' => ['td:eq(7)','text'],
                    'type' => ['td:eq(8)',"text"],
                    'totalHours' => ['td:eq(2)','text'],
                    'teachHours' => ['td:eq(3)','text'],
                    'experimentHours' => ['td:eq(4)','text'],
                    'practiceHours' => ['td:eq(5)','text'],
                    'elseHours' => ['td:eq(6)','text'],
                    'classId' => ["td:eq(0)","text","",function($content){
                        $pattern = '/\d+/';
                        if(preg_match_all($pattern, $content, $match)){
                            $content = $match[0][0];
                        }
                        return $content;
                    }],
                    'count' => ['td:eq(-5)','text'],
                    'weeks' => ['td:eq(-4)','text'],
                    'beginWeek' => ['td:eq(-4)','text','',function($content){
                        $pattern = '/\d+/';
                        if(preg_match_all($pattern, $content, $match)){
                            $content = $match[0][0];
                        }
                        return $content;
                    }],
                    'endWeek' => ['td:eq(-4)','text','',function($content){
                        $pattern = '/\d+/';
                        if(preg_match_all($pattern, $content, $match)){
                            $content = $match[0][1];
                        }
                        return $content;
                    }],
                    'SODWeek' => ['td:eq(-3)','text','',function($content){
                        if($content){
                            $content = $content=="双" ? 2 : 1;
                        }else{
                            $content = 0;
                        }
                        return $content;
                    }]
                ])->query()->getData(function($it) use ($term, $classId){
                    $it['stuClassId'] = $classId;
                    $it['beginYear'] = $term["beginYear"];
                    $it['term'] = $term["termOrder"]-1;
                    return $it;
                });
                return $i['tr'][0];
            });
            return $item;
        })->all();
    }

    public function getValidateCode(){
        return Captcha::getImgCaptcha($this->validateUrl);
    }

    protected function initPostData($grade,$instituteId,$majorId,$classId,$term){
        return [
            'hidNJ' => '',
            'hidBJDM' => $classId,  //行政班级
            'hidXQ' => '',
            'userType' => 'SPE',
            'hidYXB' => '',
            'hidZYDM' => '',
            "hidCXLX" => 'fbj',
            'hidZZLX' => 'A4',
            'xssj' => "xssj",
            'xsrq' => 'xsrq',
            "xn" => $term["beginYear"],
            "xn1" => '',
            '_xq' => '',
            "xq_m" => $term["termOrder"]-1,
            'xn_a' => date('Y')-1,     //上一年年份
            'xn1_a' => date('Y'),   //现在的年份
            "_xq_a" => '',
            "xq_m_a" => 1,
            "sfxsym" => "xsym",
            "jslx" => '',
            "xnxq" => $term["beginYear"].",".($term["termOrder"]-1),
            "isNjQuery" => "on",
            "nj" => $grade,
            "selXQ" => 1,           //学期
            "selYXB" =>$instituteId,         //学院的编号
            "radiob" => "A4",
            "selZY" => $majorId,      //专业的编号
            "selBJ" => $classId,        //行政班级编号
            "selGS" => 2,
            "radioa" => 5,
            "chkXSDYRQ" => "on",
            "chkXSDYSJ" => "on",
            "chkXSYM" => "on",
            "randnumber" => $this->getValidateCode(),
            'menucode_current' => "",
        ];
    }
}