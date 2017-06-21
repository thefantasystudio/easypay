<?php
namespace FantasyStudio\EasyPay\WeChat;

use FantasyStudio\EasyPay\Foundation\Foundation;
use FantasyStudio\EasyPay\Foundation\Request;

class WeChatRequest implements Request
{
    use Foundation;

    private $share;
    private $api_key;

    public function __construct($raw, $key)
    {
        $parse = simplexml_load_string($raw, "SimpleXMLElement", LIBXML_NOCDATA);
        $json = json_encode($parse);
        $data = json_decode($json, TRUE);
        $this->share = $data;
        $this->api_key = $key;
    }

    public function isSuccessful()
    {
        if ($this->share["result_code"] == "SUCCESS" and $this->share["return_code"] == "SUCCESS") {
            if ($this->checkSignature($this->share, $this->api_key)) {
                return true;
            }
        }

        return false;
    }

    /**
     * 微信支付處理成功後輸出
     * @return bool|string
     */
    public function sayOK()
    {
        $result = [];
        $result["return_code"] = "return_code";
        $result["return_msg"] = "OK";

        return $this->toXML($result);
    }
    
    public function getRequestData()
    {
        return $this->share;
    }

}