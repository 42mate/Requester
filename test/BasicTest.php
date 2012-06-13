<?php
require_once 'bootstrap.php';

class BasicTest extends PHPUnit_Framework_TestCase
{
  public function testSimpleGet() {
    $request = new Requester();
    $response = json_decode($request->execute('GET', BASE_URL . '/get?id=test'));
    $this->assertNotEquals(false, $response, 'Result is false');
    $this->assertEquals('test', $response->args->id);
    $response = json_decode($request->execute('GET', BASE_URL . '/get', null, array('id' => 'test')));
    $this->assertNotEquals(false, $response, 'Result is false');
    $this->assertEquals('test', $response->args->id);
  }

  public function testSimplePost() {
    $request = new Requester();
    $response = json_decode($request->execute('POST', BASE_URL . '/post', 'arg1=test&arg2=2'));
    $this->assertNotEquals(false, $response, 'Result is false');
    $this->assertEquals('test', $response->form->arg1);

    //Array Params Test
    $response = json_decode($request->execute('POST', BASE_URL . '/post', array('arg1' => 'test', 'arg2' => 2)));
    $this->assertNotEquals(false, $response, 'Result is false');
    $this->assertEquals('test', $response->form->arg1);
  }

  public function testSimplePut() {
    $request = new Requester();
    $response = json_decode($request->execute('PUT', BASE_URL . '/put', 'arg1=test&arg2=2'));
    $this->assertNotEquals(false, $response, 'Result is false');
    $this->assertEquals('test', $response->form->arg1);

    //Array Params Test
    $response = json_decode($request->execute('PUT', BASE_URL . '/put', array('arg1' => 'test', 'arg2' => 2) ));
    $this->assertNotEquals(false, $response, 'Result is false');
    $this->assertEquals('test', $response->form->arg1);
  }

  public function testSimpleDelete() {
    $request = new Requester();
    $response = json_decode($request->execute('DELETE', BASE_URL . '/delete',
            array('arg1' => 'test', 'arg2' => 2), 'id=1'));
    $this->assertNotEquals(false, $response, 'Result is false');
    $this->assertEquals('1', $response->args->id);
    $this->assertEquals('arg1=test&arg2=2', $response->data);
  }

  public function testSimpleHead() {
    $request = new Requester();
    $response = $request->execute('HEAD', BASE_URL . '/get?id=test');
    $isOk = (strpos($response, 'HTTP/1.1 200 OK') === false)?false:true;
    $this->assertNotEquals(false, $isOk, 'Result is false');
  }

  public function testSave() {
    $request = new Requester();
    $request->save(dirname(__FILE__) . '/resources/testFile', 'http://www.google.com/images/srpr/logo3w.png');
    $downloaded = file_exists(dirname(__FILE__) . '/resources/testFile');
    $this->assertNotEquals(false, $downloaded, 'The file wasnt downloaded');
    unlink(dirname(__FILE__) . '/resources/testFile');
  }

  public function testPing() {
    $request = new Requester();
    $this->assertTrue($request->ping('http://www.google.com'), 'Fail pingin Google');
    $this->assertEquals(false, $request->ping('zarlanga.com'), 'Should fail');
  }

  public function testHttps() {
    $request = new Requester();
    $request->setOptionSsl(dirname(__FILE__) . '/resources/ca/google2.pem');
    $this->assertTrue($request->ping('https://www.google.com.ar'), 'Fail pingin Google with HTTPS');
  }

}

