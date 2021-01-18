<?php


namespace app\lib\validate;


class TokenGet extends BaseValidate
{
    protected $rules = [
        'code' => 'require|isNotEmpty'
    ];

    protected $msg = [
        'code' => '没有code，无法获取token'
    ];
}