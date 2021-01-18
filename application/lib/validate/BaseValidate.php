<?php


namespace app\lib\validate;


use app\lib\exception\ParameterException;
use think\facade\Request;
use think\Validate;

class BaseValidate extends Validate
{
    /**
     * Notes:基类验证器的方法，使得其他验证器继承
     * Author: hua-bang
     * Date: 2020/8/14
     * Time: 12:42
     * @throws ParameterException
     * @return true 验证通过返回true
     */
    public function goCheck(){
        //获取所有的参数
        $params = Request::param();
        $result = $this->batch()->check($params);
        if(!$result){
            throw new ParameterException([
                'msg' => $this->error
            ]);
        }else{
            return true;
        }
    }

    protected function isPositiveInteger($value,$rule='',$data='',$field='')
    {
        if(is_numeric($value)&&is_int($value+0)&&($value+0>0)){
            return true;
        }else{
            return false;
        }
    }

    protected function isNotEmpty($value,$rule='',$data='',$field='')
    {
        if(empty($value)){
            return false;
        }else{
            return true;
        }
    }

    public function isMobile($value){
        $rule = '^1(3|4|5|7|8)[0-9]\d{8}$^';
        $result = preg_match($rule,$value);
        return $result? true : false;
    }
}