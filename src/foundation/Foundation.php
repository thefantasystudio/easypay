<?php
namespace FantasyStudio\EasyPay\Foundation;

use FantasyStudio\EasyPay\AliPay\AliPayResponse;
use FantasyStudio\EasyPay\WeChat\WeChatResponse;
use \GuzzleHttp\Client;

/**
 * Class Foundation
 * @package Fantasy\EasyPay
 * @version 1.0
 * @copyright FantasyStudio
 * @author AndyLee <leefongyun@gmail.com>
 */
trait Foundation
{
    /**
     * 随机字符串
     * @param int $length
     * @return string
     */
    function random($length = 16)
    {
        $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    /**
     * 发送支付网络请求
     * @param string $url 请求的URL
     * @param string $method 请求的method
     * @param array  $data 请求数据
     * @param string $ca_path 是否使用证书
     * @throws RuntimeException
     * @return object
     */
    public function sendRequest($url, $method = "POST", $data, $ca_path = "")
    {

        $client = new Client();

        if ($this->gateway == "wechat") {

            $pre = $this->preProcess();
            $query = array_merge($pre, $data);
            $query["sign"] = $this->makeSignature($query, $this->api_key);
            $xml = $this->toXML($query);
            $headers = ['body' => $xml, 'Content-Type' => 'text/xml; charset=UTF8'];
            if (!empty($ca_path)) {
                $headers["cert"] = $ca_path;
            }
            $response = $client->request($method, $url, $headers);
            return new WeChatResponse($response, $query, $this->api_key);


        } elseif ($this->gateway == "alipay") {

            $query = $this->preProcess();
            $query["method"] = $this->method;
            $query["biz_content"] = json_encode($data);
            $query["sign"] = $this->makeSignature($query, $this->private_key);

            $response = $client->request("POST", $url, [
                "query" => $query
            ]);
            return new AliPayResponse($response, $query, $this->private_key);
        }

    }

    /**
     * array to xml
     * @param array $data
     * @return bool|string
     */
    public function toXML($data)
    {
        if (!is_array($data) || count($data) == 0) {
            return false;
        }

        $xml = "<xml>";
        foreach ($data as $key => $val) {
            if (is_numeric($val)) {
                $xml .= "<" . $key . ">" . $val . "</" . $key . ">";
            } else {
                $xml .= "<" . $key . "><![CDATA[" . $val . "]]></" . $key . ">";
            }
        }
        $xml .= "</xml>";
        return $xml;
    }


    public function getConfig($model)
    {
        $result = new \stdClass();
        $result->merchant_id = $model->merchant_id;
        $result->type = $model->type;
        $result->subject = $model->subject;

        if ($this->gateway == "wechat") {
            if ($model->type == "partner") {
                //服务商类型的账户
                if ($model->pid > 0) {
                    $partner = $model->getPartner;
                    if ($partner) {
                        $result->sub_appid = $model->app_id;
                        $result->sub_mch_id = $model->mch_id;
                        $result->app_id = $partner->app_id;
                        $result->mch_id = $partner->mch_id;
                        $result->secret_key = $partner->secret_key;
                        $result->pem = $partner->pem;
                    }
                }

                return $result;
            }
            return $model;
        } elseif ($this->gateway == "alipay") {
            if ($model->type == "partner") {
                if ($model->pid > 0) {
                    $partner = $model->getPartner;
                    if ($partner) {
                        $result->app_id = $model->app_id;
                        $result->private_key = $model->private_key;
                        $result->public_key = $model->public_key;
                        $result->system_id = $partner->app_id;
                    }
                }
                return $result;
            }

            return $model;
        }

    }

    /**
     * 生成签名
     * @param array  $param
     * @param string $key
     * @return string
     */
    public function makeSignature($param, $key)
    {

        ksort($param);

        if ($this->gateway == "wechat") {

            $sign_str = urldecode(http_build_query($param)) . "&key=" . $key;
            $sign = md5($sign_str);

        } elseif ($this->gateway == "alipay") {

            $stringToBeSigned = "";
            $i = 0;
            foreach ($param as $k => $v) {
                if (false === $this->checkEmpty($v) && "@" != substr($v, 0, 1)) {

                    if ($i == 0) {
                        $stringToBeSigned .= "$k" . "=" . "$v";
                    } else {
                        $stringToBeSigned .= "&" . "$k" . "=" . "$v";
                    }
                    $i++;
                }
            }

            unset ($k, $v);
            $res = "-----BEGIN RSA PRIVATE KEY-----\n" .
                wordwrap($key, 64, "\n", true) .
                "\n-----END RSA PRIVATE KEY-----";

            if ($this->sign_type == "RSA") {
                openssl_sign($stringToBeSigned, $sign, $res);
            } else {
                openssl_sign($stringToBeSigned, $sign, $res, OPENSSL_ALGO_SHA256);
            }
            $sign = base64_encode($sign);
        }

        return $sign;

    }

    /**
     * 校验$value是否非空
     *  if not set ,return true;
     *    if is null , return true;
     **/
    protected function checkEmpty($value)
    {
        if (!isset($value))
            return true;
        if ($value === null)
            return true;
        if (trim($value) === "")
            return true;

        return false;
    }

    /**
     * 格式化返回
     * @param        $message
     * @param        $status
     * @param        $command
     * @param string $trade_no
     * @return array
     * @author Andylee <andylee@tiidian.com>
     */
    public function encodeResult($message, $status, $command, $trade_no = "")
    {
        $result = [
            "message" => $message,
            "status" => $status,
            "command" => $command,
            "trade_no" => $trade_no
        ];

        return $result;
    }

    /**
     * 检测签名
     * @param $param
     * @param $key
     * @return mixed
     * @author Andylee <andylee@tiidian.com>
     */
    public function checkSignature($param, $key)
    {
        $origin_sign = $param["sign"];
        unset($param["sign"]);
        ksort($param);
        $new_sign = md5(urldecode(http_build_query($param)) . "&key=" . $key);
        if ($origin_sign == strtoupper($new_sign)) {
            return true;
        } else {
            return false;
        }
    }

    public function checkAliPayNotifyMessage($param, $pub_key)
    {
        $sign = $param["sign"];
        $param["sign"] = null;
        $param["sign_type"] = null;
        ksort($param);
        $query_string = urldecode(http_build_query($param));

        $res = "-----BEGIN PUBLIC KEY-----\n" .
            wordwrap($pub_key, 64, "\n", true) .
            "\n-----END PUBLIC KEY-----";

        $result = openssl_verify($query_string, base64_decode($sign), $res);
        if ($result == 1) {
            return true;
        }
        return false;
    }

    public function get_client_ip()
    {
        $ip_address = '';
        if (isset($_SERVER['HTTP_CLIENT_IP']))
            $ip_address = $_SERVER['HTTP_CLIENT_IP'];
        else if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
            $ip_address = $_SERVER['HTTP_X_FORWARDED_FOR'];
        else if (isset($_SERVER['HTTP_X_FORWARDED']))
            $ip_address = $_SERVER['HTTP_X_FORWARDED'];
        else if (isset($_SERVER['HTTP_FORWARDED_FOR']))
            $ip_address = $_SERVER['HTTP_FORWARDED_FOR'];
        else if (isset($_SERVER['HTTP_FORWARDED']))
            $ip_address = $_SERVER['HTTP_FORWARDED'];
        else if (isset($_SERVER['REMOTE_ADDR']))
            $ip_address = $_SERVER['REMOTE_ADDR'];
        else
            $ip_address = '0.0.0.0';
        return $ip_address;
    }
}