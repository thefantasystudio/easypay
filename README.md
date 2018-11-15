# EasyPay

轻松集成支付宝,微信支付 到您的业务逻辑中。


#### 我该怎么下载安装

```php
composer require fantasystudio/easypay
```

#### 我想看看一些代码

```php
use \FantasyStudio\EasyPay\AliPay\Qr\Qr;

$qr = new Qr();
$qr->setAppId("201703300571632");
$qr->setSignType("RSA");
$qr->setPrivateKey("私钥内容,不要携带-----BEGIN RSA PRIVATE KEY-----");
$qr->purchase([
    "total_amount" => 0.01, "out_trade_no" => "2120960179264092", "subject" => "subjectaa"
]);

$result = $qr->sendPaymentRequest(); //发起扫码支付

var_dump($result->getRequestData()); //获取请求数据
var_dump($result->getResponseData()); //获取网关响应数据
var_dump($result->isSuccessful()); // 请求是否成功


```



#### 开始集成

| 分类   | 网关     | 描述           | 相关链接                                     |
| ---- | ------ | ------------ | ---------------------------------------- |
| 支付宝  | Qr     | 扫码支付         | [官方文档](https://docs.open.alipay.com/194/106078/) \| [使用文档](https://github.com/thefantasystudio/easypay/wiki/%E6%94%AF%E4%BB%98%E5%AE%9D-%E6%89%AB%E7%A0%81%E6%94%AF%E4%BB%98) |
| 支付宝  | Pos    | 条码支付         | [官方文档](https://docs.open.alipay.com/194/106039/) \| [使用文档](https://github.com/thefantasystudio/easypay/wiki/%E6%94%AF%E4%BB%98%E5%AE%9D-%E6%9D%A1%E7%A0%81%E6%94%AF%E4%BB%98) |
| 支付宝  | JSApi    | 支付宝容器内支付(类似微信JSSDK支付)         | [官方文档](https://docs.open.alipay.com/api_1/alipay.trade.create) \| [使用文档](https://github.com/thefantasystudio/easypay/wiki/%E6%94%AF%E4%BB%98%E5%AE%9D-%E5%AE%B9%E5%99%A8%E5%86%85%E6%94%AF%E4%BB%98) |
| 支付宝  | Web    | 电脑网站支付       | [官方文档](https://docs.open.alipay.com/270) \| [未完成]() |
| 支付宝  | Wap    | 手机网站支付       | [官方文档](https://docs.open.alipay.com/203) \| [未完成]() |
| 支付宝  | Wap    | APP支付       | [官方文档](https://docs.open.alipay.com/204/105465/) \| [使用文档](https://github.com/thefantasystudio/easypay/wiki/%E6%94%AF%E4%BB%98%E5%AE%9DApp%E6%94%AF%E4%BB%98) |
| 微信支付 | Pos    | 刷卡支付         | [官方文档](https://pay.weixin.qq.com/wiki/doc/api/micropay.php?chapter=5_1) \| [使用文档](https://github.com/thefantasystudio/easypay/wiki/%E5%BE%AE%E4%BF%A1-%E5%88%B7%E5%8D%A1%E6%94%AF%E4%BB%98) |
| 微信支付 | JSApi  | 公众号支付(JSSDK) | [官方文档](https://pay.weixin.qq.com/wiki/doc/api/jsapi.php?chapter=7_1) \| [使用文档](https://github.com/thefantasystudio/easypay/wiki/%E5%BE%AE%E4%BF%A1-%E5%85%AC%E4%BC%97%E5%8F%B7%E6%94%AF%E4%BB%98) |
| 微信支付 | Native | 扫码支付         | [官方文档](https://pay.weixin.qq.com/wiki/doc/api/native.php?chapter=6_1) \| [使用文档](https://github.com/thefantasystudio/easypay/wiki/%E5%BE%AE%E4%BF%A1-%E6%89%AB%E7%A0%81%E6%94%AF%E4%BB%98) |
| 微信支付 | App    | APP支付        | [官方文档](https://pay.weixin.qq.com/wiki/doc/api/app/app.php?chapter=8_1) \| [使用文档](https://github.com/thefantasystudio/easypay/wiki/%E5%BE%AE%E4%BF%A1-APP%E6%94%AF%E4%BB%98) |
| 微信支付 | H5     | H5支付         | [官方文档](https://pay.weixin.qq.com/wiki/doc/api/H5.php?chapter=15_1) \| [使用文档](https://github.com/thefantasystudio/easypay/wiki/%E5%BE%AE%E4%BF%A1-H5%E6%94%AF%E4%BB%98) |
