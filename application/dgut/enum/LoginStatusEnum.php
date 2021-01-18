<?php


namespace app\dgut\enum;


class LoginStatusEnum
{
    //  cas身份登录成功
    const LOGIN_SUCCESS = 1;

    //  cas验证失败
    const LOGIN_FAIL = 0;

    //  cas身份成功，需要验证码
    const LOGIN_NEED_VERIFY = 23;
}