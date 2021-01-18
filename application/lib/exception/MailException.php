<?php


namespace app\lib\exception;


class MailException extends BaseException
{
    public $code = "500";
    public $msg = "遇到小问题，邮箱发送失败。";
    public $errorCode = 50004;
}