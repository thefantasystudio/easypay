<?php
use FantasyStudio\EasyPay\AliPay\Pos\Pos;

$pos = new Pos();
$pos->setAppId("2017022005775662");
$pos->setPrivateKey("MIICXAIBAAKBgQDQXi9KGowqYsezGWfuFpmFd2Xppqt26hGjrcIyIPFkDh1zjZhh
cWWU6VK+q1nuxAFBNEOA7x65oQGrJmfafrDTGBf31OgWJONtQCyXmiQb5myjbMXt
xtP+LZUUqdXpr3O9SZKamtoHvrCzrBsOd/mM1JnrslA2fcMWZAuRl+1BswIDAQAB
AoGBAJwfxg0fQSDNLNZ8VcwmcNd5NApTEKykC515p0VKO7R9gwG++YshnTxras1k
tJjbkhkaIO4tvPbXtdlYiH5FaRUYChkSFg+/M+4soNJmdjmoAJgOpVCfnUj3ZzxZ
P/S29N9Z4CYBE+25DaW1Ohdw9JcINH/s6sSTQbyUree8emHpAkEA7CP3dGP943dI
eOJcUBXLKAzFNpgHR1DGCe01FRUw26t4fU7M5zaUowOTeY8Fi2mmWmYDD6wXZrYs
vLNi75qDTQJBAOHkSGHllwLD5xdx255CF+1/EcUlvCPnlUQzkhvOtaHIlHbE4B/Y
sb2y9fG0bevjsaw3cVOL1dJqXL72pjpqWP8CQH395q7kFveBklplNCnKpv10atjp
HqEPWMq9FGBuUQYzo/L/01XeCko7wzjNdYDf2tFKsoFvKYE03APGlpgO0dUCQCr5
dxvIxfXstyYqrxFomYzBQ+wxxZ0/DZKwDPflV4Cz3CrMQadNXmMsjMWzAcaCxuZw
lcrDK6agPYzG99DdMpkCQHDkY3JiZhWn1bNBDyzPifKuq9XiGCnXucSlFcANvqsZ
O4ELzmIHdFIdiR+o78tpZs/S8OMzJBOI9BaLdYa2xSE=");
$pos->setSignType("RSA");

$pos->purchase([
    "total_amount" => 0.01, "scene" => "bar_code", "auth_code" => "288324275423799429", "out_trade_no" => "201923123112", "subject" => "subjectaa"
]);

$result = $pos->sendPaymentRequest();
var_dump($result->getRequestData()); //获取请求支付宝网关数据
var_dump($result->getResponseData()); //获取支付宝返回数据
var_dump($result->isSuccessful()); //获取请求状态
