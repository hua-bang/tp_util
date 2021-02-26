<?php


namespace app\dgut\service;


use app\lib\exception\BaseException;
use app\utils\controller\Captcha;
use app\utils\controller\VCode;
use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use Mohuishou\ImageOCR\Image;
use Mohuishou\ImageOCR\ImageOCR;
use QL\QueryList;
use think\Console;
use think\facade\Cache;
use function GuzzleHttp\Psr7\str;

class Cet
{
    protected $cookiesJar;
    protected $client;

    private $validateUrl = "http://appquery.neea.edu.cn/api/verify/get";
    private $queryValidateUrl = "http://cet-bm.neea.cn/Home/VerifyCodeImg?COLLCC=3048913976&";
    private $baseListUrl = "http://appquery.neea.edu.cn/api/result/list?";
    private $baseDetailUrl = "http://appquery.neea.edu.cn/api/result/data?";
    private $basePhotoUrl = "http://appquery.neea.edu.cn/api/result/photo?";
    private $ticketUrl = "http://cet-bm.neea.cn/Home/ToQueryTestTicket";
    private $scoreUrl = "http://cachecloud.neea.cn/cet/query?data=";


    public function __construct()
    {
        $this->cookiesJar = new CookieJar();
        $this->client = new Client();
    }


    public function getValidateImg($key) {
        $ql = QueryList::getInstance();
        $res = $ql->get($this->validateUrl,[],['cookies' => $this->cookiesJar])->getHtml();
        $this->saveCookiesToCache($key,serialize($this->cookiesJar));
        header('Content-Type:image/jpeg');
        return $res;
    }

    public function  getQueryValidate($key) {
        $ql = QueryList::getInstance();
        $res = $ql->get($this->queryValidateUrl,[],['cookies' => $this->cookiesJar])->getHtml();
        $this->saveCookiesToCache($key,serialize($this->cookiesJar));
        header('Content-Type:image/jpeg');
        return $res;
    }

    public function getImgByToken($token) {
        $url = $this->basePhotoUrl.'poken='.$token;
        $res = $this->client->request('get',$url,[
            'headers' => [
                "Referer" => "http://cjcx.neea.edu.cn/",
                "Host" => "appquery.neea.edu.cn"
            ]
        ])->getBody();
        header('Content-Type:image/jpeg');
        return $res;
    }

    public function getScoreList($code,$key,$type,$idCard,$name) {
        $this->cookiesJar = unserialize(Cache::get($key));
        $url = $this->baseListUrl."&subject=".$type."&xm=".urlencode($name)."&sfz=".$idCard."&verify=".$code;
        $res = $this->client->request('get',$url,[
            'headers' => [
                "Referer" => "http://cjcx.neea.edu.cn/",
                "Host" => "appquery.neea.edu.cn"
            ],
            'cookies' => $this->cookiesJar
        ]);
        return json_decode($res->getBody()->getContents(),true);
    }

    public function saveCookiesToCache($key,$jar) {
        Cache::set($key,$jar);
    }

    public function getScoreDetail($token) {
        $url = $this->baseDetailUrl.'token='.$token;
        $res = $this->client->request('get',$url,[
            'headers' => [
                "Referer" => "http://cjcx.neea.edu.cn/",
                "Host" => "appquery.neea.edu.cn"
            ]
        ]);
        return json_decode($res->getBody()->getContents(),true);
    }

    public function getTicket($data,$key) {
        $this->cookiesJar = unserialize(Cache::get($key));
        $res = $this->client->request('post',$this->ticketUrl,[
            'headers' => [
                "Referer" => "http://cjcx.neea.edu.cn/",
                "Host" => "appquery.neea.edu.cn"
            ],
            "form_params" => $data,
            'cookies' => $this->cookiesJar
        ]);
        $res = json_decode($res->getBody()->getContents(),true)["ExceuteResultType"];
        if($res==-1) {
            throw new BaseException([
               "msg" => "验证码错误,请重试"
            ]);
        }
        return json_decode(((json_decode($res->getBody()->getContents(),true))["Message"]))[0]->TestTicket;
    }

    public function getSingleScore($data) {
        $url = $this->scoreUrl.$data;
        $res = $this->client->request('get',$url,[
            'headers' => [
                "Referer" => "http://cjcx.neea.edu.cn/",
                "Host" => "appquery.neea.edu.cn"
            ]
        ]);
        $obj = [];
        $result = $res->getBody()->getContents();
        if(strstr($result,"err")) {
            throw new BaseException([
                "msg" => "检查身份证，姓名是否正确"
            ]);
        }
        $result = str_replace(["result.callback({","});"],"",$result);
        $arr = explode(",",$result);
        foreach ($arr as $k => $v) {
            $array = explode(":",$v);
            $key = $array[0];
            $val = $array[1];
            $obj[$key] = str_replace("'","",$val);
        }
        return $obj;
    }

    public function getScoreIdCard($key,$data) {
        $ticket = $this->getTicket($data,$key);
        $data = "CET_202012_DANGCI,${ticket},${data['Name']}";
        return $this->getSingleScore($data);
    }
}