<?php


namespace app\lib\exception;


class TableException extends BaseException
{
    public $code = "500";
    public $msg = "遇到小问题，课表获取失败。";
    public $errorCode = 50004;
}