<?php
namespace FantasyStudio\EasyPay\Foundation;

/**
 * Interface PaymentComm 通用支付接口
 * @package Fantasy\EasyPay\Foundation
 * @copyright FantasyStudio
 * @author AndyLee <leefongyun@gmail.com>
 */
interface PaymentComm
{

    /**
     * 设置支付参数
     * @param array $order 订单信息
     * @return mixed
     */
    public function purchase($order);

    /**
     * 发起支付请求
     * @return mixed
     */
    public function sendPaymentRequest();

//    /**
//     * 处理异步通知
//     * @return mixed
//     */
//    public function processNotifyRequest();

    /**
     * 查询订单
     * @param array $data
     * @return mixed
     */
    public function queryOrderState($data);

}