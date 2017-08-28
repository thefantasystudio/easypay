<?php
namespace FantasyStudio\EasyPay\Foundation;

/**
 * 支付响应事件
 * Interface Response
 * @package Fantasy\EasyPay\Foundation
 * @copyright FantasyStudio
 * @author AndyLee <leefongyun@gmail.com>
 */
interface Response
{
    public function isSuccessful();

    public function getRequestData();

    public function getResponseData();

}