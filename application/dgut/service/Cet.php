<?php


namespace app\dgut\service;


use app\utils\controller\Captcha;
use app\utils\controller\VCode;
use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use Mohuishou\ImageOCR\Image;
use Mohuishou\ImageOCR\ImageOCR;
use QL\QueryList;
use think\facade\Cache;

class Cet
{
    protected $cookiesJar;
    protected $client;

    private $validateUrl = "http://appquery.neea.edu.cn/api/verify/get";
    private $baseListUrl = "http://appquery.neea.edu.cn/api/result/list?";
    private $baseDetailUrl = "http://appquery.neea.edu.cn/api/result/data?";


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
}