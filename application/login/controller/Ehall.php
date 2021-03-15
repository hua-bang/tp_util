<?php


namespace app\login\controller;
use app\dgut\service\BaseService;
use GuzzleHttp\Cookie\CookieJar;
use QL\QueryList;
use GuzzleHttp\Client;


class Ehall extends BaseService
{
    private $casJar;    //cas登录时候token需要对应页面的cookies
    private $EHALLjar;
    private $client;
    private $ehallLoginUrl = "https://cas.dgut.edu.cn/home/Oauth/getToken/appid/ehall/state/home.html";

    public function __construct()
    {
        $this->casJar = new CookieJar();
        $this->EHALLjar = new CookieJar();
        $this->client = new Client();
    }

    public function getTokenByEHALL(){
        $response = $this->client->request('GET',$this->ehallLoginUrl,['cookies'=>$this->casJar,'verify' => false]);
        $data = $response->getBody();
        $ql = QueryList::html($data);
        $html = $ql->find('script')->htmls();
        $tokenStr = $html->all()[7];
        return substr($tokenStr,127,32);
    }

    public function userData($username,$password){
        $user['username'] = $username;
        $user['password'] = $password;
        $user['__token__'] = $this->getTokenByEHALL();
        $user['wechat_verify'] = '';
        return $user;
    }


    protected function loginEHALLUrl($user) {
        $response = $this->client->request('POST',$this->ehallLoginUrl,[
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
        $res = json_decode(json_decode($casToAppIdMessage,true),true);
        if($res['code']!=1) {
            throw new TokenException(['msg'=>'登录失败，请检验用户名，密码。']);
        };
        return $res;
    }

    //查询token的入口函数
    public function loginEHALLByCas() {
        $username = input("post.username");
        $password = input("post.password");
        $user = $this->userData($username,$password);
        $data = $this->loginEHALLUrl($user);
        // 获得登录成功后重定向的url
        $redirectUrl = $data['info'];
        $response = $this->client->request('get',$redirectUrl,[
            'verify' => false,
            'allow_redirects' => false
        ]);
        $url = $response->getHeader("location")[0];
        return substr($url,43);
        //return $this->saveCookiesByRedirectUrl($redirectUrl);
    }


                
}