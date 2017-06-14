<?php
namespace FantasyStudio\EasyPay\WeChat;

use FantasyStudio\EasyPay\Foundation\Foundation;
use FantasyStudio\EasyPay\Foundation\Response;
use GuzzleHttp\Psr7\Response as R;

class WeChatResponse implements Response
{
    use Foundation;
    public $response_data;
    public $request_data;
    private $key;

    public function __construct(R $response, $request_data, $key)
    {

        $body = (string)$response->getBody();
        $parse = simplexml_load_string($body, "SimpleXMLElement", LIBXML_NOCDATA);
        $json = json_encode($parse);
        $response_data = json_decode($json, TRUE);

        $this->response_data = $response_data;
        $this->request_data = $request_data;
        $this->key = $key;

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \RuntimeException("Got an runtime error, can't decoded response data,"
                . "json_last_error is" . json_last_error()
                . ", please see http://php.net/manual/en/function.json-last-error.php");
        }

    }

    public function getResponseData()
    {
        return $this->response_data;
    }

    public function getRequestData()
    {
        return $this->request_data;
    }

    public function isSuccessful()
    {
        if ($this->response_data["return_code"] == "SUCCESS" and $this->response_data["result_code"] == "SUCCESS") {
            if ($this->checkSignature($this->response_data, $this->key)) {
                return true;
            }

            return false;
        }

        return false;
    }
}