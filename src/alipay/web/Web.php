<?php
namespace FantasyStudio\EasyPay\AliPay\Web;

use FantasyStudio\EasyPay\AliPay\AliPayComm;
use FantasyStudio\EasyPay\Foundation\Foundation;
use FantasyStudio\EasyPay\Foundation\PaymentComm;

/**
 * Class Web 支付宝电脑网站支付
 * @package FantasyStudio\EasyPay\AliPay\Web
 * @version 1.0
 * @see https://docs.open.alipay.com/common/105901
 * @copyright FantasyStudio
 * @author AndyLee <leefongyun@gmail.com>
 */
class Web implements AliPayComm, PaymentComm
{
    use Foundation;

    public $app_id;
    public $public_key;
    private $private_key;
    public $notify_url;
    public $order;
    public $gateway = "alipay";
    public $postCharset = "UTF-8";


    public $gateway_url = "https://openapi.alipay.com/gateway.do";

    public function preProcess()
    {
        $pre_order = [];
        $pre_order["app_id"] = $this->app_id;
        $pre_order["method"] = "alipay.trade.page.pay";
        $pre_order["charset"] = "utf-8"; //TODO 仅支持utf8
        $pre_order["timestamp"] = date("Y-m-d H:i:s");
        $pre_order["version"] = "1.0";
        $pre_order["sign_type"] = "RSA";

        return $pre_order;
    }


    /**
     * 建立请求，以表单HTML形式构造（默认）
     * @param $form_params 请求参数数组
     * @return 提交表单HTML文本
     */
    protected function buildRequestForm($form_params)
    {

        $form_params["sign"] = $this->makeSignature($form_params, $this->private_key);
        $sHtml = "<form id='alipaysubmit' name='alipaysubmit' action='" . $this->gateway_url . "?charset=" . trim($this->postCharset) . "' method='POST'>";
        foreach ($form_params as $key => $val) {
            $sHtml .= "<input type='hidden' name='" . $key . "' value='" . $val . "'/>";
        }
        $sHtml = $sHtml . "<input type='submit' value='ok' style='display:none;''></form>";
        $sHtml = $sHtml . "<script>document.forms['alipaysubmit'].submit();</script>";

        return $sHtml;
    }


    public function sendPaymentRequest()
    {
        return $this->buildRequestForm($this->order);
    }

    public function purchase($order)
    {
        $data = [];
        $order["product_code"] = "FAST_INSTANT_TRADE_PAY";

        $required = [
            "out_trade_no", "total_amount", "subject"
        ];

        foreach ($required as $k => $field) {
            if (!array_key_exists($field, $order)) {
                throw new \InvalidArgumentException("The {$field} field is required");
            }
        }

        $data["biz_content"] = json_encode($order);
        $this->order = array_merge($this->preProcess(), $data);

    }

    public function queryOrderState($data)
    {

    }

    public function reverseOrder($order)
    {

    }


    public function setPrivateKey($content)
    {
        $this->private_key = $content;
    }

    public function setNotifyUrl($url)
    {

    }

    public function setAppId($app_id)
    {
        $this->app_id = $app_id;
    }

    public function refundOrder($order)
    {

    }

}