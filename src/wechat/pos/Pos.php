<?php
namespace FantasyStudio\EasyPay\WeChat\Pos;

use FantasyStudio\EasyPay\Foundation\Foundation;
use FantasyStudio\EasyPay\Foundation\PaymentComm;
use FantasyStudio\EasyPay\Foundation\Standard;
use FantasyStudio\EasyPay\WeChat\WeChatComm;

/**
 * 微信刷卡支付
 * @package FantasyStudio\EasyPay\Wechat\Pos
 * @version 1.0
 * @copyright FantasyStudio
 * @author AndyLee <leefongyun@gmail.com>
 */
class Pos implements WeChatComm, PaymentComm
{
    use Foundation;

    private $app_id;
    private $api_key;
    private $mch_id;
    public $order = [];
    public $gateway = "wechat";

    /**
     * 提交刷卡支付URL
     * @var string
     */
    public $micropay_url = "https://api.mch.weixin.qq.com/pay/micropay";

    /**
     * 查询订单URL
     * @var string
     */
    public $orderquery_url = "https://api.mch.weixin.qq.com/pay/orderquery";
    /**
     * 申请退款URL
     * @var string
     */
    public $refund_url = "https://api.mch.weixin.qq.com/secapi/pay/refund";

    /**
     * 撤销订单URL
     * @var string
     */
    public $reverse_url = "https://api.mch.weixin.qq.com/secapi/pay/reverse";

    /**
     * 查询退款
     * @var string
     */
    public $refund_query_url = "https://api.mch.weixin.qq.com/pay/refundquery";

    public function preProcess()
    {
        $pre_data["appid"] = $this->app_id;
        $pre_data["mch_id"] = $this->mch_id;
        $pre_data["nonce_str"] = $this->random();
        return $pre_data;
    }

    public function purchase($data)
    {
        $pre_data = $this->preProcess();

        $pre_data["sign_type"] = "MD5";
        $pre_data["spbill_create_ip"] = $this->get_client_ip() == "::1" ? "127.0.0.1" : $this->get_client_ip();
        $order = array_merge($pre_data, $data);

        if (empty($order["appid"])) {
            throw new \InvalidArgumentException("The appid field is required");
        }
        if (empty($order["mch_id"])) {
            throw new \InvalidArgumentException("The mch_id field is required");
        }

        if (empty($order["total_fee"])) {
            throw new \InvalidArgumentException("The total_fee field is required");
        }
        if (!array_key_exists("auth_code", $order) or empty($order["auth_code"])) {
            throw new \InvalidArgumentException("The auth_code field is required");
        }

        if (!array_key_exists("out_trade_no", $order) or empty($order["out_trade_no"])) {
            throw new \InvalidArgumentException("The out_trade_no field is required");
        }

        $this->order = $order;
    }

    public function sendPaymentRequest()
    {
        return $this->sendRequest($this->micropay_url, "POST", $this->order, "");
    }

    public function queryOrderState($data)
    {
        return $this->sendRequest($this->orderquery_url, "POST", $data, "");
    }

    public function queryRefundState($data)
    {
        return $this->sendRequest($this->refund_query_url, "POST", $data, "");
    }

    public function refundOrder($order, $ca_path)
    {
        return $this->sendRequest($this->refund_url, "POST", $order, $ca_path);
    }


    public function reverseOrder($order, $ca_path)
    {
        return $this->sendRequest($this->reverse_url, "POST", $order, $ca_path);
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