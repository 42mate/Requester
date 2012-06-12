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
    $response = $this->requester->execute(BASE_URL . '/get?id=test', 'HEAD');
    $isOk = (strpos($response, 'HTTP/1.1 200 OK') === false)?false:true;
    $this->assertNotEquals(false, $isOk, 'Result is false');

    $response = $this->requester->execute('https://httpbin.org/', 'HEAD');
    $isOk = (strpos($response, 'HTTP/1.1 200 OK') === false)?false:true;
    $this->assertNotEquals(false, $isOk, 'Result is false');
  }

}