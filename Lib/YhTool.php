<?php
/**
 * Created by PhpStorm.
 * User: weiyaheng
 * Date: 16/3/16
 * Time: 下午1:41
 */
namespace Robotx\Lib;

class YhTool{

    /**
     * 单个curl远程访问请求
     * @param $url
     * @param $data
     * @param string $method
     * @param null $header
     * @return bool|mixed
     * @throws \Exception
     */
    public static function curl($url, $data, $method = 'POST',$header=null)
    {

        if (is_array($data)) {
            $req_str = http_build_query($data);
        } else {
            $req_str = $data;
        }
        if ($method !== 'POST' && $data) {
            $url .= '?' . $req_str;
        }
        //p(urldecode($url));die;
        $ch = curl_init();
        if($header){
            curl_setopt($ch, CURLOPT_HTTPHEADER  , $header);
        }
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/35.0.1916.153 Safari/537.36');
        if ($method == 'POST') {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $req_str);
        }

        $res = curl_exec($ch);
        $resCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if (($resCode > 399 && $resCode < 500) || $err_no = curl_errno($ch)) {
            throw new \Exception(sprintf("curl失败，远程请求错误，返回代码：%s", $resCode ));
        }
        curl_close($ch);
        return $res;
    }




}