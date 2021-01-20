<?php


namespace app\dgut\controller;


use app\dgut\service\Jw;
use app\utils\controller\ResponseUtil;

class Score
{
    /**
     * Notes: 根据用户名，密码，登录到教务系统，自己生成token，保存用户的登录态
     * Author: hua-bang
     * Date: 2021/1/20
     * Time: 0:54
     * @return string|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getTokenFromJW() {
        $username = input("post.username");
        $password = input("post.password");
        $token = (new Jw())->loginJwByCas($username,$password);
        return (new ResponseUtil(['data'=> ['token' => $token]]))->toResult();
    }

    public function getScore() {
        $configData = input("post.config");
        return (new ResponseUtil(['data' => (new Jw())->getScore($configData)]))->toResult();
    }
}