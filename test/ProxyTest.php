<?php

require_once 'bootstrap.php';

class ProxyTest extends PHPUnit_Framework_TestCase
{

  private $requester;

  public function __constructor() {
    $this->requester = new Requester();
    $this->setProxyParams();
  }

  private function setProxyParams() {
    $proxy = $GLOBALS['proxy'];
    $this->requester->setOptionProxy($proxy);
  }

  public function testGet() {
    $response = $this->requester->head(BASE_URL . '/get?id=test');
    $isOk = (strpos($response, 'HTTP/1.1 200 OK') === false)?false:true;
    $this->assertNotEquals(false, $isOk, 'Result is false');

    $response = $this->requester->head('https://httpbin.org/');
    $isOk = (strpos($response, 'HTTP/1.1 200 OK') === false)?false:true;
    $this->assertNotEquals(false, $isOk, 'Result is false');
  }

}