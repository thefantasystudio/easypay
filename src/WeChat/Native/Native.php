<?php
namespace FantasyStudio\EasyPay\WeChat\Native;

use FantasyStudio\EasyPay\Foundation\Foundation;
use FantasyStudio\EasyPay\Foundation\PaymentComm;
use FantasyStudio\EasyPay\WeChat\WeChatComm;
use FantasyStudio\EasyPay\WeChat\WeChatRequest;

/**
 * Class Native 扫码支付
 * @package FantasyStudio\EasyPay\WeChat\Native
 * @version 1.0
 * @copyright FantasyStudio
 * @author AndyLee <leefongyun@gmail.com>
 */
class Native implements WeChatComm, PaymentComm
{
    use Foundation;

    private $app_id;
    private $api_key;
    private $mch_id;
    public $order = [];
    public $gateway = "wechat";

    /**
     * 统一下单URL
     * @var string
     */
    public $unifiedorder_url = "https://api.mch.weixin.qq.com/pay/unifiedorder";
    /**
     * 订单查询URL
     * @var string
     */
    public $orderquery_url = "https://api.mch.weixin.qq.com/pay/orderquery";
    /**
     * 关闭订单URL
     * @var string
     */
    public $closeorder_url = "https://api.mch.weixin.qq.com/pay/closeorder";
    /**
     * 申请退款URL
     * @var string
     */
    public $refund_url = "https://api.mch.weixin.qq.com/secapi/pay/refund";
    /**
     * 查询退款URL
     * @var string
     */
    public $refund_query_url = "https://api.mch.weixin.qq.com/pay/refundquery";

    public function preProcess()
    {
        $pre_data["appid"] = $this->app_id;
        $pre_data["mch_id"] = $this->mch_id;
        $pre_data["nonce_str"] = "uBFpfrllIoFxWQnz";//$this->random();
        return $pre_data;
    }

    public function purchase($data)
    {
        $pre_data = $this->preProcess();

        $pre_data["sign_type"] = "MD5";
        $pre_data["spbill_create_ip"] = $this->get_client_ip() == "::1" ? "127.0.0.1" : $this->get_client_ip();
        $order = array_merge($pre_data, $data);

        $require_field = [
            "appid", "mch_id", "nonce_str", "body","product_id", "out_trade_no", "total_fee", "spbill_create_ip", "notify_url",
            "trade_type"
        ];

        foreach ($require_field as $key => $field) {
            if (!array_key_exists($field, $order)) {
                throw new \InvalidArgumentException("The {$field} field is required");
            }
        }
        
        $this->order = $order;
    }

    public function sendPaymentRequest()
    {
        return $this->sendRequest($this->unifiedorder_url, "POST", $this->order, "");
    }

    public function queryOrderState($order)
    {
        return $this->sendRequest($this->orderquery_url, "POST", $order, "");
    }

    public function refundOrder($order, $ca_path)
    {
        return $this->sendRequest($this->refund_url, "POST", $order, $ca_path);

    }

    public function queryRefundState($order)
    {
        return $this->sendRequest($this->refund_query_url, "POST", $order);
    }

    public function reverseOrder($order, $ca_path)
    {
        return $this->sendRequest($this->closeorder_url, "POST", $order, $ca_path);
    }

    public function closeOrder($order)
    {
        return $this->sendRequest($this->closeorder_url, "POST", $order);
    }

    public function refundQuery($order)
    {
        return $this->sendRequest($this->refundquery_url, "POST", $order, "");
    }

    public function processNotifyMessage($raw_data)
    {
        return new WeChatRequest($raw_data, $this->api_key);
    }


    public function setApiKey($id)
    {
        $this->api_key = $id;
    }

    public function setMchId($mch_id)
    {
        $this->mch_id = $mch_id;
    }

    public function setAppId($app_id)
    {
        $this->app_id = $app_id;
    }
}