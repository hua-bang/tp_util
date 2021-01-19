<?php


namespace app\dgut\controller;
use app\dgut\service\Cet as CetService;
use think\facade\Cache;

class Cet
{

    public function getCetScoreList($code,$key) {
        $name = input("post.name");
        $idCard = input("post.idCard");
        $type = input("post.type");
        return (new CetService())->getScoreList($code,$key,$type,$idCard,$name);
    }

    public function getScoreDetail($token) {
        return (new CetService())->getScoreDetail($token);
    }

    public function getCetCaptcha($key) {
        $image = (new CetService())->getValidateImg($key);
        echo $image;exit;
    }

    public function getCetCookies($key) {
    }

    public function setCetCookies($key) {
        return Cache::set($key,["a"=>[
            "b" => [
                "c" => 1
            ]
        ]]);
    }
}