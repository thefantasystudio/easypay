<?php
namespace FantasyStudio\EasyPay\AliPay;

interface AliPayComm
{
    /**
     * 设置支付宝APPID
     * @param string $app_id
     * @return mixed
     */
    public function setAppId($app_id);

    /**
     * 设置支付宝异步通知地址
     * @param string $url
     * @return mixed
     */
    public function setNotifyUrl($url);


    /**
     * 设置私钥
     * @param string $path 私钥路径
     * @return mixed
     */
    public function setPrivateKey($path);

    /**
     * 退款
     * @param array $order;
     * @return mixed
     */
    public function refundOrder($order);

    /**
     * 撤销订单
     * @param array $order;
     * @return mixed
     */
    public function reverseOrder($order);

//    public function queryRefundState($order);
}