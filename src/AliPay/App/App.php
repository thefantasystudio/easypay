<?php
namespace FantasyStudio\EasyPay\AliPay\App;

use FantasyStudio\EasyPay\AliPay\AliPayRequest;
use FantasyStudio\EasyPay\AliPay\AliPayComm;
use FantasyStudio\EasyPay\Foundation\Foundation;
use FantasyStudio\EasyPay\Foundation\PaymentComm;

/**
 * Class App 手机app支付2.0
 * @package FantasyStudio\EasyPay\AliPay\App
 * @version 1.0
 * @author Andylee <andy.dev@aliyun.com>
 */
class App implements AliPayComm, PaymentComm
{
    use Foundation;

    public $app_id;
    public $public_key;
    private $private_key;
    public $notify_url;
    public $sign_type;
    public $order;
    public $base = [];
    public $method;
    public $gateway = "alipay";
    public $postCharset = "UTF-8";

    public $enable_koubei_promo = false; //是否开启口碑折扣 @see https://open.koubei.com/#/solution?type=codeServer&no=koubei_qrcode_orderdishes

    public $gateway_url = "https://openapi.alipay.com/gateway.do";

    public function setSignType($type)
    {
        $this->sign_type = $type;
    }

    public function setBaseData($data)
    {
        $this->base = $data;
    }

    public function preProcess()
    {
        $arr = array_merge([
            "app_id" => $this->app_id,
            "format" => "JSON",
            "charset" => "utf-8",
            "sign_type" => $this->sign_type,
            "timestamp" => date("Y-m-d H:i:s"),
            "version" => "1.0",
            "notify_url" => $this->notify_url,
        ], $this->base);

        if ($this->enable_koubei_promo == true) {
            $arr["promo_params"] = "{\"kborder_flag\":\"order\"}";
        }
        return $arr;
    }

    public function setPublicKey($key)
    {
        $this->public_key = $key;
    }

    public function queryRefundState($order)
    {
        $this->method = "alipay.trade.fastpay.refund.query";
        return $this->sendRequest($this->gateway_url, "POST", $order, "", $this->private_key);
    }

    public function purchase($order)
    {
        $require_field = [
            "out_trade_no", "total_amount", "subject", "product_code"
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
        $this->notify_url = $url;
    }

    public function setPrivateKey($content)
    {
        $this->private_key = $content;
    }

    public function sendPaymentRequest()
    {
        $this->method = "alipay.trade.app.pay";
        $query = $this->preProcess();
        $query["method"] = $this->method;
        $query["biz_content"] = json_encode($this->order,JSON_UNESCAPED_UNICODE);
        ksort($query);
        $query["sign"] = $this->makeSignature($query, $this->private_key);

        return http_build_query($query);
    }

    public function queryOrderState($data)
    {
        $this->method = "alipay.trade.query";
        return $this->sendRequest($this->gateway_url, "POST", $data, "", $this->private_key);
    }

    public function enableKoubeiPromo($bool)
    {
        $this->enable_koubei_promo = $bool;
    }

    public function processNotifyMessage($message)
    {
        return new AliPayRequest($message, $this->public_key);
    }
}