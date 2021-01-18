<?php


namespace app\lib\exception;


class ProjectException extends BaseException
{
    public $code = 500;
    public $msg = "该任务未找到";
    public $errorCode = 50011;
}