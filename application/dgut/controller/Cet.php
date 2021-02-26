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

    public function getPhoto($token){
        echo (new CetService())->getImgByToken($token);
        exit;
    }

    public function getQueryValidatePhoto($key) {
        $image = (new CetService())->getQueryValidate($key);
        echo $image;exit;
    }

    public function getTicket($code,$key){
        $data["IDNumber"] = input("post.IDNumber");
        $data["Name"] = input("post.Name");
        $data['provinceCode'] = 44;
        $data['IDTypeCode'] = 1;
        $data['verificationCode'] = $code;
        return (new CetService())->getTicket($data,$key);
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

    public function getScoreByTicket() {
        $ticket = input("post.ticket");
        $name = input("post.name");
        $data = "CET_202012_DANGCI,${ticket},${name}";
        return (new CetService())->getSingleScore($data);
    }

    public function getScoreByIdCard($code,$key) {
        $data["IDNumber"] = input("post.IDNumber");
        $data["Name"] = input("post.Name");
        $data['provinceCode'] = 44;
        $data['IDTypeCode'] = 1;
        $data['verificationCode'] = $code;
        return (new CetService())->getScoreIdCard($key,$data);
    }
}