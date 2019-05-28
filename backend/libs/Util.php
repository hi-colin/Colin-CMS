<?php
/**
 * User: Colin
 * Time: 2019/5/13 13:12
 */

namespace backend\libs;


class Util
{
    /**
     * 终止并完成http请求；客户端终止等待完成请求
     * 后续代码可以继续运行；例如日志、统计等代码；后续输出将不再生效；
     */
    public function http_close(){
        self::ignore_timeout();
        if(function_exists('fastcgi_finish_request')) {
            fastcgi_finish_request();
        } else {
            header("Connection: close");
            header("Content-Length: ".ob_get_length());
            ob_start();
            echo str_pad('',1024*5);
            ob_end_flush();
            flush();
        }
    }

    /**
     * 设置超时时间和内存限制
     */
    public static function ignore_timeout(){
        @ignore_user_abort(true);
        @ini_set("max_execution_time",48 * 60 * 60);
        @set_time_limit(48 * 60 * 60);//set_time_limit(0)  2day
        @ini_set('memory_limit', '4000M');//4G;
    }

    /**
     * 根据QQ号获取用户名和头像等信息
     * @param $qq
     * @return mixed
     */
    function getUserInfoByQq($qq)
    {
        $url = 'http://r.qzone.qq.com/fcg-bin/cgi_get_portrait.fcg?uins=' . $qq;
        $data = file_get_contents($url);
        $data = iconv('GB2312', 'UTF-8', $data);
        $pattern =  '/portraitCallBack\((.*)\)/is';
        preg_match($pattern, $data, $result);
        $res = $result[1];
        $res = json_decode($res, true);
        return $res;
    }



}