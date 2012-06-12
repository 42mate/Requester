<?php
require_once 'bootstrap.php';

class BasicTest extends PHPUnit_Framework_TestCase
{
  public function testSimpleGet() {
    $request = new Requester();
    $response = json_decode($request->execute(BASE_URL . '/get?id=test', 'GET'));
    $this->assertNotEquals(false, $response, 'Result is false');
    $this->assertEquals('test', $response->args->id);
  }

  public function testSimplePost() {
    $request = new Requester();
    $request->setOptionData('arg1=test&arg2=2');
    $response = json_decode($request->execute(BASE_URL . '/post', 'POST'));
    $this->assertNotEquals(false, $response, 'Result is false');
    $this->assertEquals('test', $response->form->arg1);

    //Array Params Test
    $request->setOptionData(array('arg1' => 'test', 'arg2' => 2));
    $response = json_decode($request->execute(BASE_URL . '/post', 'POST'));
    $this->assertNotEquals(false, $response, 'Result is false');
    $this->assertEquals('test', $response->form->arg1);
  }

  public function testSimplePut() {
    $request = new Requester();
    $request->setOptionData('arg1=test&arg2=2');
    $response = json_decode($request->execute(BASE_URL . '/put', 'PUT'));
    $this->assertNotEquals(false, $response, 'Result is false');
    $this->assertEquals('test', $response->form->arg1);

    //Array Params Test
    $request->setOptionData(array('arg1' => 'test', 'arg2' => 2));
    $response = json_decode($request->execute(BASE_URL . '/put', 'PUT'));
    $this->assertNotEquals(false, $response, 'Result is false');
    $this->assertEquals('test', $response->form->arg1);
  }

  public function testSimpleDelete() {
    $request = new Requester();
    $request->setOptionData('arg1=test&arg2=2');
    $response = json_decode($request->execute(BASE_URL . '/delete?id=1', 'DELETE'));
    $this->assertNotEquals(false, $response, 'Result is false');
    $this->assertEquals('1', $response->args->id);
    $this->assertEquals('arg1=test&arg2=2', $response->data);

    //Array Params Test
    $request->setOptionData(array('arg1' => 'test', 'arg2' => 2));
    $response = json_decode($request->execute(BASE_URL . '/delete?id=1', 'DELETE'));
    $this->assertNotEquals(false, $response, 'Result is false');
    $this->assertEquals('1', $response->args->id);
    $this->assertEquals('arg1=test&arg2=2', $response->data);
  }

  public function testSimpleHead() {
    $request = new Requester();
    $response = $request->execute(BASE_URL . '/get?id=test', 'HEAD');
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

}

