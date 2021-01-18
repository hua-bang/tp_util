<?php


namespace app\lib\exception;


class ModelException extends BaseException
{
    public $code = "500";
    public $msg = "数据库模型配置有问题。";
    public $errorCode = 50021;
}