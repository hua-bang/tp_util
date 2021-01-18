<?php


namespace app\utils\controller;


class CurlUtil
{
    public function postRequest($url,$data){
        // $ch1 = curl_init();
        // $timeout = 10000;
        // curl_setopt ( $ch1, CURLOPT_URL, $url );
        // curl_setopt ( $ch1, CURLOPT_POST, 1 );
        // curl_setopt ( $ch1, CURLOPT_RETURNTRANSFER, 1 );
        // curl_setopt ( $ch1, CURLOPT_CONNECTTIMEOUT, $timeout );
        // curl_setopt ( $ch1, CURLOPT_SSL_VERIFYPEER, FALSE );
        // curl_setopt ( $ch1, CURLOPT_SSL_VERIFYHOST, false );
        // curl_setopt ( $ch1, CURLOPT_POSTFIELDS, $data);
        // $result=curl_exec($ch1);
        // curl_close($ch1);
        // return $result;
        
        $curl = curl_init();  
		//设置URL和相应的选项   
		curl_setopt($curl, CURLOPT_URL, $url);  
		curl_setopt ( $curl, CURLOPT_SAFE_UPLOAD, true); 
		if (!empty($data)){  
		curl_setopt($curl, CURLOPT_POST, 1);  
		curl_setopt($curl, CURLOPT_POSTFIELDS, $data);  
		}  
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);  
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); 
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		//执行curl，抓取URL并把它传递给浏览器  
		$output = curl_exec($curl);  
		//关闭cURL资源，并且释放系统资源  
		curl_close($curl);  
		return $output;  
    }

    /**
     * Notes:
     * Author: hua-bang
     * Date: 2020/8/18
     * Time: 10:35
     * @param $url string 资源的网络路径
     * @param string $path 存放的path位置
     */
    public static function download($url, $path = 'images/')
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        $file = curl_exec($ch);
        curl_close($ch);
        $filename = pathinfo($url, PATHINFO_BASENAME);
        $resource = fopen($path . $filename, 'a');
        fwrite($resource, $file);
        fclose($resource);
        return $path.$filename;
    }
}