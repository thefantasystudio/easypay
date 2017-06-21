<?php
namespace FantasyStudio\EasyPay\AliPay;

use FantasyStudio\EasyPay\Foundation\Foundation;
use FantasyStudio\EasyPay\Foundation\Request;

class AliPayRequest implements Request
{
    use Foundation;

    private $share;
    private $pub_key;

    public function __construct($raw, $key)
    {

        $this->share = $raw;
        $this->pub_key = $key;
    }

    public function isSuccessful()
    {
        $status = [
            "TRADE_SUCCESS", //商户支持退款 支付成功返回
            "TRADE_FINISHED" //商户不支持退款 或者 已经过期
        ];

        if (in_array($this->share["trade_status"], $status)) {
            if ($this->checkAliPayNotifyMessage($this->share, $this->pub_key)) {
                return true;
            }
        }

        return false;
    }

    /**
     * 支付宝支付處理成功後輸出
     * @return bool|string
     */
    public function sayOK()
    {
        return "success";
    }

    public function getRequestData()
    {
        return $this->share;
    }

}