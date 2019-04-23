<?php
/**
 * Created by PhpStorm.
 * User: JIANG
 * Date: 2019/4/23
 * Time: 21:31
 */
namespace App\Handlers;
class SlugTranslateHandler
{

    public function translate($text)
    {
// 实例化 HTTP 客户端
        $curl = curl_init();

// 初始化配置信息
        $api = 'http://api.fanyi.baidu.com/api/trans/vip/translate?';
        $appid = config('services.baidu_translate.appid');
        $key = config('services.baidu_translate.key');
        $salt = time();

// 根据文档，生成 sign
// http://api.fanyi.baidu.com/api/trans/product/apidoc
// appid+q+salt+密钥 的MD5值
        $sign = md5($appid. $text . $salt . $key);
// 构建请求参数
        $query = http_build_query([
            "q" => $text,
            "from" => "zh",
            "to" => "en",
            "appid" => $appid,
            "salt" => $salt,
            "sign" => $sign,
        ]);
// 发送 HTTP Get 请求
        curl_setopt($curl,CURLOPT_URL,$api.$query);
        // CURLOPT_RETURNTRANSFER  设置是否有返回值
        curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
        //执行完以后的返回值
        $response = curl_exec($curl);
        //释放curl
        curl_close($curl);
//        $response = $http->get($api.$query);
        $result = json_decode($response, true);
        /**
        获取结果，如果请求成功，dd($result) 结果如下：
        array:3 [▼
        "from" => "zh"
        "to" => "en"
        "trans_result" => array:1 [▼
        0 => array:2 [▼
        "src" => "XSS 安全漏洞"
        "dst" => "XSS security vulnerability"
        ]
        ]
        ]
         **/
// 尝试获取获取翻译结果
        if (isset($result['trans_result'][0]['dst'])) {
            return str_slug($result['trans_result'][0]['dst']);
        }

    }
}