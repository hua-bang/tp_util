<?php


namespace app\utils\controller;


class Generate
{
    /**
     * Notes: 根据长度生成字符串
     * Author: hua-bang
     * Date: 2020/8/14
     * Time: 13:13
     * @param $length int 需要的得到字符串的长度
     * @return string|null 得到的随机字符串
     */
    public static function getRandChars($length){
        $str = null;
        $strPol = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwsyz";
        $max = strlen($strPol)-1;
        for ($i = 0; $i < $length; $i++){
            $str .= $strPol[rand(0,$max)];
        }
        return $str;
    }


}