<?php


namespace app\lib\validate;


class UserTokenGet extends BaseValidate
{
    protected $rule = [
        "username" => "require|isNotEmpty",
        "password" => "require|isNotEmpty",
        "secret_key" => "require|isNotEmpty"
    ];

    protected $msg = [
        "username" => "用户名必须填写",
        "password" => "密码必须填写",
        "secret_key" => "缺少登录的相关凭证"
    ];
}