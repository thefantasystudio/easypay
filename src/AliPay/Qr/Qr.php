<?php
namespace FantasyStudio\EasyPay\AliPay\Qr;

use FantasyStudio\EasyPay\AliPay\AliPayComm;
use FantasyStudio\EasyPay\AliPay\AliPayRequest;
use FantasyStudio\EasyPay\AliPay\AliPayResponse;
use FantasyStudio\EasyPay\Foundation\Foundation;
use FantasyStudio\EasyPay\Foundation\PaymentComm;

class Qr implements AliPayComm, PaymentComm
{
    use Foundation;

    public $app_id;
    public $public_key;
    private $private_key;
    public $notify_url;
    public $sign_type;
    public $order;
    public $method;
    public $gateway = "alipay";
    public $postCharset = "UTF-8";

    public $gateway_url = "https://openapi.alipay.com/gateway.do";

    public function setSignType($type)
    {
        $this->sign_type = $type;
    }

    public function preProcess()
    {
        $query_params = [
            "app_id" => $this->app_id,
            "format" => "JSON",
            "charset" => "utf-8",
            "sign_type" => $this->sign_type,
            "timestamp" => date("Y-m-d H:i:s"),
            "version" => "1.0"
        ];

        if (!empty($this->notify_url)) {
            $query_params["notify_url"] = $this->notify_url;
        }

        return $query_params;
    }

    public function setNotifyUrl($url)
    {
        $this->notify_url = $url;
    }

    public function setPrivateKey($key)
    {
        $this->private_key = $key;
    }

    public function setPublicKey($key)
    {
        $this->public_key = $key;
    }


    public function queryOrderState($order)
    {
        $this->method = "alipay.trade.query";
        return $this->sendRequest($this->gateway_url, "POST", $order, "", $this->private_key);
    }

    public function sendPaymentRequest()
    {
        $this->method = "alipay.trade.precreate";
        return $this->sendRequest($this->gateway_url, "POST", $this->order, "");
    }

    public function queryRefundState($order)
    {
        $this->method = "alipay.trade.fastpay.refund.query";
        return $this->sendRequest($this->gateway_url, "POST", $order, "");
    }

    public function purchase($order)
    {
        $require_field = [
            "out_trade_no", "total_amount", "subject"
        ];

        foreach ($require_field as $key => $field) {
            if (!array_key_exists($field, $order)) {
                throw new \InvalidArgumentException("The {$field} field is required, see detail https://docs.open.alipay.com/api_1/alipay.trade.precreate");
            }
        }

        $this->order = $order;
    }

    public function refundOrder($order)
    {
        $this->method = "alipay.trade.refund";
        return $this->sendRequest($this->gateway_url, "POST", $order, "", $this->private_key);
    }

    public function reverseOrder($order)
    {
        $this->method = "alipay.trade.cancel";
        return $this->sendRequest($this->gateway_url, "POST", $order, "", $this->private_key);
    }

    public function setAppId($app_id)
    {
        $this->app_id = $app_id;
    }

    public function processNotifyMessage($message)
    {
        return new AliPayRequest($message, $this->public_key);
    }

    public function checkSign($param, $key)
    {
        $sign = $param["sign"];
        $param["sign"] = null;
        $param["sign_type"] = null;
        ksort($param);
        $query_string = urldecode(http_build_query($param));

        $res = "-----BEGIN PUBLIC KEY-----\n" .
            wordwrap($key, 64, "\n", true) .
            "\n-----END PUBLIC KEY-----";

        $result = openssl_verify($query_string, base64_decode($sign), $res);
        if ($result == 1) {
            return true;
        }
        return false;
        
    }
}