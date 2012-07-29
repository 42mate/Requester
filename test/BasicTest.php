<?php
require_once 'bootstrap.php';

class BasicTest extends PHPUnit_Framework_TestCase
{
  
  public function testCurlException() {
    try {
      $request = new Requester();
      $request->get('http://127.0.0.1:9999/');
      $this->fail('This must never be reached');
    } catch (Exception $e) {
      $this->assertTrue(is_a($e, 'Exception'), 'Must be an exception');
      $this->assertEquals($e->getCode(), 7);
    }
  }

  public function test404Exception() {
    try { 
      $request = new Requester();
      $request->setOptionFailOnError(true);
      $request->get('http://httpbin.org/hidden-basic-auth');
      $this->fail('This must never be reached');
    } catch (Exception $e) {
      $this->assertTrue(is_a($e, 'Exception'), 'Must be an exception');
      $this->assertEquals($e->getCode(), 22); //Always 22 for any http error
    }
  }
  
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
    $this->assertEquals(false, $request->ping('http://127.0.0.1:9999/'), 'Should fail, you sholdnt have a WebServer in the http://127.0.0.1:999999999/ url');
  }

  public function testHttps() {
    $request = new Requester();
    $request->setOptionSsl(dirname(__FILE__) . '/resources/ca/google2.pem');
    $this->assertTrue($request->ping('https://www.google.com.ar'), 'Fail pingin Google with HTTPS');
  }
  
  public function testArrayResponse() {
    $request = new Requester();
    $request->setOptionResponseType(Requester::RESPONSE_ARRAY);
    $result = $request->get('http://httpbin.org/get');
    $this->assertTrue(is_array($result), 'Must be an array');
    $this->assertEquals($result['url'], 'http://httpbin.org/get', 'Url should be http://httpbin.org/get');
    $this->assertEquals($result['http_code'], 200);
    $this->assertTrue(isset($result['headers']));
    $this->assertTrue(isset($result['content']));
    $jsonParsed = json_decode($result['content']);
    $this->assertNotNull($jsonParsed);
    $this->assertEquals($jsonParsed->url, 'http://httpbin.org/get', 'Url is not in the response');
    
    //Back to String
    $request->setOptionResponseType(Requester::RESPONSE_RAW);
    $result = $request->get('http://httpbin.org/get');
    $this->assertTrue(is_string($result), 'Must be an String');
  }
  
  public function testLastStatusCode() {
    $request = new Requester();
    $this->assertEquals($request->getLastHttpCode(), 0, 'Must be 0, no request was made yet');
    $result = $request->get('http://httpbin.org/get');
    $this->assertEquals($request->getLastHttpCode(), 200, 'Must be 200');
  }

}

