<?php


namespace app\dgut\service;


use app\utils\controller\Generate;
use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use QL\QueryList;
use think\facade\Request;

class Jw extends BaseService
{
    private $casJar;    //cas登录时候token需要对应页面的cookies
    private $jwJar;     //jw系统登录是基于session
    private $client;
    private $loginUrl = "https://cas.dgut.edu.cn/home/Oauth/getToken/appid/jwyd.html";  //中央认证地址
    private $timeChooseArr = ['sjxz1','sjxz2','sjxz3'];
    private $scoreChooseArr = ['yscj','yxcj'];

    /**
     * Jw constructor.
     */
    public function __construct()
    {
        $this->casJar = new CookieJar();
        $this->jwJar = new CookieJar();
        $this->client = new Client();
    }

    /**
     * Notes: 得到登录时候需要的token,同时需要保存访问得到的cookies
     * Author: hua-bang
     * Date: 2021/1/20
     * Time: 0:37
     * @return false|string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function getCasLoginToken() {
        $response = $this->client->request('GET',$this->loginUrl,['cookies'=>$this->casJar,'verify' => false]);
        $data = $response->getBody();
        $ql = QueryList::html($data);
        $html = $ql->find('script')->htmls();
        $tokenStr = $html->all()[7];
        return substr($tokenStr,124,32);
    }

    /**
     * Notes: 根据用户名,密码进行中央认证登录,登录成功返回一个token保存登录态
     * Author: hua-bang
     * Date: 2021/1/20
     * Time: 0:37
     * @param $username string 用户名
     * @param $password string 密码
     * @return string|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function loginJwByCas($username,$password) {
        $user = $this->generateUserData($username,$password);
        $data = $this->loginJwUrl($user);
        // 获得登录成功后重定向的url
        $redirectUrl = $data['info'];
        return $this->saveCookiesByRedirectUrl($redirectUrl);
    }

    /**
     * Notes: 封装user请求数据
     * Author: hua-bang
     * Date: 2021/1/20
     * Time: 0:40
     * @param $username
     * @param $password
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function generateUserData($username,$password) {
        $user['username'] = $username;
        $user['password'] = $password;
        $user['__token__'] = $this->getCasLoginToken();
        $user['wechat_verify'] = '';
        return $user;
    }

    protected function loginJwUrl($user) {
        $response = $this->client->request('POST',$this->loginUrl,[
            'form_params' => $user,
            'headers' => [
                'Accept' => 'application/json, text/javascript, */*; q=0.01',
                'X-Requested-With' => 'XMLHttpRequest',
                'User-Agent' => 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.130 Mobile Safari/537.36',
                'Content-Type' =>  'application/x-www-form-urlencoded; charset=UTF-8'
            ],
            'verify' => false,
            'cookies' => $this->casJar,
            'allow_redirects' => true
        ]);
        $casToAppIdMessage = $response->getBody()->getContents();
        return json_decode(json_decode($casToAppIdMessage,true),true);
    }

    /**
     * Notes: 保存token
     * Author: hua-bang
     * Date: 2021/1/20
     * Time: 0:51
     * @param $redirectUrl
     * @return string|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function saveCookiesByRedirectUrl($redirectUrl) {
        $this->client->request('get',$redirectUrl,[
            'cookies' => $this->jwJar,
        ]);
        $token = Generate::getRandChars(32);
        $this->saveCookiesToCache($token,serialize($this->jwJar));
        return $token;
    }

    public function getScore($configData) {
        $this->jwJar = $this->getCookiesFromCacheByKey(Request::header('token'));
        $data = $this->generateRequestScoreData($configData);
        return $this->getScoreDataFromStr($this->getScorePageContent($data));
    }

    protected function getScoreDataFromStr($str) {
        $ql = QueryList::getInstance();
        $rule = [
            'score' => ['body table:eq(1)>tbody>tr','html','',function($content){return iconv("gb2312","utf-8//IGNORE", $content);}]
        ];
        $data = $ql->html($str)->rules($rule)->query()->getData(function ($item) {
            $item = (new QueryList())->html($item['score'])->rules([
                'name' => ['td:eq(1)','html'],
                'credit' => ['td:eq(2)','text'],
                'type' =>['td:eq(3)','text'],
                'quality' =>['td:eq(4)','text'],
                'assessment_method' => ['td:eq(5)','text'],
                'get_method' => ['td:eq(6)','text'],
                'score' => ['td:eq(7)','text'],
            ])->queryData();
            return $item;
        });
        return $data;

    }

    protected function getScorePageContent($data) {
        $url = "http://jwyd.dgut.edu.cn/student/xscj.stuckcj_data.jsp";
        $response = $this->client->request('post',$url,[
            'form_params' => $data,
            'headers' => [
                'Accept' => 'application/json, text/javascript, */*; q=0.01',
                'X-Requested-With' => 'XMLHttpRequest',
                'User-Agent' => 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/79.0.3945.130 Mobile Safari/537.36',
                'Content-Type' =>  'application/x-www-form-urlencoded; charset=UTF-8'
            ],
            'cookies' => $this->jwJar,
        ]);
        return $response->getBody()->getContents();
    }

    protected function generateRequestScoreData($configData) {
        $data['sjxz'] = $this->timeChooseArr[$configData['time_type']];    //时间选择
        $data['ysyx'] = $this->scoreChooseArr[$configData['score_type']];   //成绩选择
        $data['zx'] = "1";
        $data['fx'] = "1";
        $data['xn'] = $configData['begin_year'];
        $data['xn1'] = $configData['end_year'];
        $data['xq'] = $configData['term'];
        $data['ysyxS'] = "on";
        $data['sjxzS'] = "on";
        $data['zxC'] = "on";
        $data['fxC'] = "on";
        $data['menucode_current'] = "";
        return $data;
    }

}