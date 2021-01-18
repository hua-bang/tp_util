<?php


namespace app\utils\controller;


class ResponseUtil
{
    protected $code = 200;
    protected $errorCode = 0;
    protected $msg = "获取成功.";
    protected $data = null;

    function __construct($params = []){
        if(!is_array($params)){
            return ;
            //   throw new Exception("参数必须是数组");
        }
        if(array_key_exists('code',$params)){
            $this->code = $params['code'];
        }
        if(array_key_exists('msg',$params)){
            $this->msg = $params['msg'];
        }
        if(array_key_exists('data',$params)){
            $this->data = $params['data'];
        }
        if(array_key_exists('errorCode',$params)){
            $this->data = $params['errorCode'];
        }
    }

    public function toResult(){
        return json(['errorCode'=>$this->errorCode,'msg'=>$this->msg,'data'=>$this->data],$this->code);
    }
}