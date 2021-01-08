<?php
namespace fuyuezhen\wechat;

use fuyuezhen\wechat\util\Cached;
use fuyuezhen\wechat\util\Request;
use fuyuezhen\wechat\util\Util;
use fuyuezhen\wechat\config\UrlConfig;

/** 
* 微信Api
* @author fuyuezhen <976066889@qq.com>
* @created 2020-12-10
*/ 
class Api
{

    /**
     * 发送模板消息
     * @param string $template 内容
     * @return void
     */
    public static function sendTemplateMessage($template, $access_token)
    {
        $temp   = urldecode(json_encode($template));
        $url    = "https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=" . $access_token;
        $result = Request::curl($url, $temp);
        return $result;
    }

    /**
     * 获取用户信息
     * @return void
     */
    public static function getUserInfo($openid, $access_token)
    {
        $options = [
            'access_token'  => $access_token,
            'openid'        => $openid,
            'lang'=>'zh_CN'
        ];

        $url  = UrlConfig::BIN_USERINFO_URL . http_build_query($options);
        $info = Request::curl($url);
        if(isset($info['errcode'])){
            jsAlert(\json_encode($info), false);
        }
        return $info;
    }

    /**
     * 签名
     * @param string $mp_key api接口的公众号nonstr
     * @return void
     */
    public static function getSignPackage($appid, $jsapi_ticket)
    {
        try{
            $url          = empty($url) ? request()->url(true) : $url;
            $timestamp    = time();
            $nonceStr     = Util::getRandomString();

            // 这里参数的顺序要按照 key 值 ASCII 码升序排序
            $string = "jsapi_ticket=$jsapi_ticket&noncestr=$nonceStr&timestamp=$timestamp&url=$url";
            $signature = sha1($string);

            $signPackage = [
                "appId"     => $appid,
                "nonceStr"  => $nonceStr,
                "timestamp" => $timestamp,
                "url"       => $url,
                "signature" => $signature,
                "rawString" => $string,
                "jsapi_ticket" => $jsapi_ticket
            ];
            return $signPackage;
        }catch(\Exception $e){
            return false;
        }
    }

}