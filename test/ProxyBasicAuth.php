<?php

require_once 'bootstrap.php';

class ProxyBasicAuth extends PHPUnit_Framework_TestCase
{
  private $requester;

  public function __constructor() {
    $this->requester = new Requester();
    $this->setProxyParams();
  }

  private function setProxyParams() {
    $proxy_basic = $GLOBALS['proxy_basic'];
    $this->requester->setOptionProxy($proxy_basic);
  }

  public function testGet() {
    $response = $this->requester->execute(BASE_URL . '/get?id=test', 'HEAD');
    $isOk = (strpos($response, 'HTTP/1.1 200 OK') === false)?false:true;
    $this->assertNotEquals(false, $isOk, 'Result is false');

    $response = $this->requester->execute('https://httpbin.org/', 'HEAD');
    $isOk = (strpos($response, 'HTTP/1.1 200 OK') === false)?false:true;
    $this->assertNotEquals(false, $isOk, 'Result is false');
  }

}