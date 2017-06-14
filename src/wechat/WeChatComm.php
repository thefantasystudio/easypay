<?php
namespace FantasyStudio\EasyPay\WeChat;

/**
 * 微信支付公共方法
 * Interface WeiXinCommon
 * @package FantasyStudio\EasyPay\WeiXin
 * @copyright FantasyStudio
 * @author AndyLee <leefongyun@gmail.com>
 */
interface WeChatComm
{
    /**
     * 设置微信公众账号ID
     * @param string $app_id 微信公众号ID
     * @return mixed
     */
    public function setAppId($app_id);

    /**
     * 设置微信商户号
     * @param string $mch_id 商户ID
     * @return mixed
     */
    public function setMchId($mch_id);

    /**
     * 设置微信支付密钥
     * @param string $key 微信支付密钥
     * @return mixed
     */
    public function setApiKey($key);
}