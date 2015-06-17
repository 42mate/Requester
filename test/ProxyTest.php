<?php

require_once 'bootstrap.php';

class ProxyTest extends PHPUnit_Framework_TestCase
{

  private $requester;

  private function setProxyParams() {
    $proxy = $GLOBALS['proxy'];
    $this->requester->setOptionProxy($proxy);
  }

  public function testGet() {
    if (!TEST_PROXY) {
 	return;
    }

    $this->requester = new Requester();
    $this->setProxyParams();

    $response = $this->requester->head(BASE_URL . '/get?id=test');
    $isOk = (strpos($response, 'HTTP/1.1 200 OK') === false)?false:true;
    $this->assertNotEquals(false, $isOk, 'Result is false');

    $response = $this->requester->head('https://httpbin.org/');
    $isOk = (strpos($response, 'HTTP/1.1 200 OK') === false)?false:true;
    $this->assertNotEquals(false, $isOk, 'Result is false');
  }

}
