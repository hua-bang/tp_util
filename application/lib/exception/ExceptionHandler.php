<?php


namespace app\lib\exception;


use Exception;
use think\exception\Handle;
use think\facade\Log;
use think\facade\Request;

class ExceptionHandler extends Handle
{

    public function render(Exception $e)
    {
        if($e instanceof BaseException){
            $code = $e->code;
            $msg = $e->msg;
            $errorCode = $e->errorCode;
            $data = $e->data;
        }else{
            if(config('app_debug')){
                return parent::render($e);
            }else{
                $code = 500;
                $msg = '服务器内部错误';
                $errorCode = 999;
                $data = null;
                $this->recordErrorLog($e);
            }
        }
        $result = [
            'msg' => $msg,
            'error_code' => $errorCode,
            'data' => $data,
            'request_url' => Request::url()
        ];
        return json($result, $code);
    }

    private function recordErrorLog(Exception $e){
        Log::record($e->getMessage(),'error');
    }
}