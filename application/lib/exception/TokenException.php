<?php


namespace app\lib\exception;


class TokenException extends BaseException
{
    public $code = "401";
    public $msg = "token无效或已经过期";
    public $errorCode = 40001;
}