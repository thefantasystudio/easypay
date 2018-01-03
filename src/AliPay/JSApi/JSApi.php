<?php
namespace FantasyStudio\EasyPay\AliPay\JSApi;

use FantasyStudio\EasyPay\AliPay\AliPayComm;
use FantasyStudio\EasyPay\Foundation\Foundation;
use FantasyStudio\EasyPay\Foundation\PaymentComm;

class JSApi implements AliPayComm, PaymentComm
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
        return [
            "app_id" => $this->app_id,
            "format" => "JSON",
            "charset" => "utf-8",
            "sign_type" => $this->sign_type,
            "timestamp" => date("Y-m-d H:i:s"),
            "version" => "1.0",
            "notify_url" => $this->notify_url
        ];
    }

    public function queryRefundState($order)
    {
        $this->method = "alipay.trade.fastpay.refund.query";
        return $this->sendRequest($this->gateway_url, "POST", $order, "", $this->private_key);
    }

    public function purchase($order)
    {
        $require_field = [
            "out_trade_no", "total_amount", "subject", "buyer_id"
        ];

        foreach ($require_field as $key => $field) {
            if (!array_key_exists($field, $order)) {
                throw new \InvalidArgumentException("The {$field} field is required, see detail https://docs.open.alipay.com/api_1/alipay.trade.pay");
            }
        }

        $this->order = $order;
    }

    public function reverseOrder($order)
    {
        $this->method = "alipay.trade.cancel";
        return $this->sendRequest($this->gateway_url, "POST", $order, "", $this->private_key);
    }

    public function refundOrder($order)
    {
        $this->method = "alipay.trade.refund";
        return $this->sendRequest($this->gateway_url, "POST", $order, "", $this->private_key);
    }

    public function setAppId($app_id)
    {
        $this->app_id = $app_id;
    }

    public function setNotifyUrl($url)
    {
        $this->notify_url;
    }

    public function setPrivateKey($content)
    {
        $this->private_key = $content;
    }

    public function sendPaymentRequest()
    {
        $this->method = "alipay.trade.create";
        return $this->sendRequest($this->gateway_url, "POST", $this->order, "", $this->private_key);
    }

    public function queryOrderState($data)
    {
        $this->method = "alipay.trade.query";
        return $this->sendRequest($this->gateway_url, "POST", $data, "", $this->private_key);
    }
}