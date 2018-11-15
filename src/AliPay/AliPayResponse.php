<?php
namespace FantasyStudio\EasyPay\AliPay;

use FantasyStudio\EasyPay\Foundation\Foundation;
use FantasyStudio\EasyPay\Foundation\Response;
use GuzzleHttp\Psr7\Response as R;


class AliPayResponse implements Response
{
    use Foundation;
    public $response_data;
    public $request_data;
    private $key;

    public function __construct(R $response, $request_data, $key)
    {

        $body = (string)$response->getBody();
        $this->request_data = $request_data;
        $response_data = json_decode($body, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \RuntimeException("Got an runtime error, can't decoded response data,"
                . "json_last_error is " . json_last_error()
                . ", please see http://php.net/manual/en/function.json-last-error.php");
        }

        $this->response_data = $response_data;
        $this->key = $key;

    }

    public function getResponseData()
    {
        return $this->response_data;
    }

    public function getRequestData()
    {
        return $this->request_data;
    }

    public function isSuccessful()
    {
        $keys = [
            "alipay.trade.pay" => "alipay_trade_pay_response",
            "alipay.trade.query" => "alipay_trade_query_response",
            "alipay.trade.refund" => "alipay_trade_refund_response",
            "alipay.trade.cancel" => "alipay_trade_cancel_response",
            "alipay.trade.fastpay.refund.query" => "alipay_trade_fastpay_refund_query_response",
            "alipay.trade.precreate" => "alipay_trade_precreate_response",
            "alipay.trade.create" => "alipay_trade_create_response"
        ];

        $method = $this->request_data["method"];
        if (array_key_exists($keys[$method], $this->response_data)) {
            if (array_key_exists("code", $this->response_data[$keys[$method]])) {
                if ($this->response_data[$keys[$method]]["code"] == "10000"
                    and $this->response_data[$keys[$method]]["msg"] == "Success"
                ) {
                    return true;
                }
            }
        }
        return false;
    }
}