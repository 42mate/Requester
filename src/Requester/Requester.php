<?php

/**
 * Class for interact with Http Requests.
 *
 * @author Casiva Agustin
 */
class Requester {

  protected $url = '';
  protected $method = 'GET';

  static protected $default_options =  array(
      CURLOPT_FRESH_CONNECT => TRUE,
      CURLOPT_RETURNTRANSFER => TRUE,
      CURLINFO_HEADER_OUT => TRUE,
      CURLOPT_SSL_VERIFYPEER => false,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_MAXREDIRS => 3,
  );

  protected $options = array();

  /**
   * Creates a Request Object
   *
   * @param String $method  A valid HTTP Method
   * @param String $url     A valid Url
   * @param Array  $options Posible entries
   *   params       : Array of parameters to by added as Query String to the Url
   *   data         : Array of data to include in the body of the request
   *   timeout      : Time in seconds to wait for the request, Default 30
   *   allow_redirects: True or false Default True
   *   max_redirects: Numeric, default 3
   *   proxy        : Array (url, auth, auth_method). Default None
   *   encoding     : String, The encoding type to pass to curl, Default ''
   */
  public function __construct($url = '', $options = array()) {
    $this->url = $url;
    $this->options = self::$default_options;

    if (isset($options['params']) && is_array($options['params'])) {
      $this->params = http_build_query($options['params']);
      $this->url .= '?' . $this->params;
    }
    $this->setOptionUrl($this->url);

    $data = '';
    if (isset($options['data']) && is_array($options['data'])) {
      $data = $options['data'];
    }
    $this->setOptionData($data);

    $timeOut = 30;
    if (isset($options['timeout']) && is_numeric($options['timeout'])) {
      $timeOut = $options['timeout'];
    }
    $this->setOptionTimeOut($timeOut);

    $max_redirects = 3;
    if (isset($options['max_redirects']) && is_numeric($options['max_redirects'])) {
      $max_redirects = (int) $options['max_redirects'];
    }

    if (isset($options['allow_redirects'])) {
      $this->setOptionAllowRedirect($max_redirects);
    }

    $proxy = FALSE;
    if (isset($options['proxy']) && is_array($options['proxy'])) {
      $proxy = $options['proxy'];
    }

    $this->setOptionProxy($proxy);
  }

  /**
   * Executes the Request
   *
   * @param String $url     : The url to hit, is optional, by def takes the internal url
   * @param String $method  : The HTTP method to use, by default use the internal Method.
   * @return String|Boolean : The content or false on failure
   */
  public function execute($url = null, $method = null) {
    if ($url !== null) {
      $this->setOptionUrl($url);
    }
    if ($method !== null) {
      $this->setOptionMethod($method);
    }
    $ch = curl_init();
    curl_setopt_array($ch, $this->options);
    $result = curl_exec($ch);
    if (curl_errno($ch) > 0) {
      $result = false;
    }
    curl_close($ch);
    return $result;
  }

  /**
   * Saves the Request in store path
   *
   * @param String $storePath : Full path to store the file
   * @param String $url       : The url to hit
   * @return boolean          : True on success False on fail
   */
  public function save($storePath, $url = null) {
    $fileContent = $this->execute($url);
    $fp = fopen($storePath,'w');
    if ($fp !== false) {
      $writeStatus = fwrite($fp, $fileContent);
      if ($writeStatus !== false) {
        fclose($fp);
        return true;
      }
    }
    return false;
  }

  /**
   * Pings to the Url to check of works
   *
   * @param  $url    : Url to hit
   * @return boolean : True on Success False on Fail
   */
  public function ping($url) {
     if ($this->execute($url, 'HEAD') !== false) {
       return true;
     }
     return false;
  }

  /**
   * Sets the Url to Hit
   * @param String     : $url
   * @return Requester
   */
  public function setOptionUrl($url) {
    $this->options[CURLOPT_URL] = $url;
    return $this;
  }

  public function setOptionMethod($method = null) {
    $this->options[CURLOPT_HEADER] = FALSE;
    if ($method !== null) {
      $this->method = $method;
    }
    switch ($this->method) {
      case 'GET':
        $this->options[CURLOPT_POST] = FALSE;
        break;
      case 'POST':
        $this->options[CURLOPT_POST] = TRUE;
        break;
      case 'HEAD':
        $this->options[CURLOPT_HEADER] = TRUE;
        $this->options[CURLOPT_NOBODY] = TRUE;
        break;
      default:
        $this->options[CURLOPT_CUSTOMREQUEST] = $this->method;
    }
    return $this;
  }

  public function setOptionProxy($proxy) {
    if ($proxy !== FALSE) {
      $this->options[CURLOPT_PROXY] = $proxy['url'];
      if (isset($proxy['auth'])) {
        $this->options[CURLOPT_PROXYAUTH] = CURLAUTH_BASIC;
        if (isset($proxy['auth_method']) && $proxy['auth_method'] === 'NTLM') {
          $this->options[CURLOPT_PROXYAUTH] = CURLAUTH_NTLM;
        }
        $this->options[CURLOPT_PROXYUSERPWD] = $proxy['auth'];
      }
    }
    return $this;
  }

  public function setOptionTimeOut($timeOut) {
    $this->options[CURLOPT_TIMEOUT] = 30;
    if (isset($timeOut)) {
      $this->options[CURLOPT_TIMEOUT] = $timeOut;
    }
    return $this;
  }

  /**
   * Sets Payload for POST requests
   *
   * @param Mixed (Array or String) $data
   * @return Requester
   */
  public function setOptionData($data) {
    if (is_array($data)) {
      $data = http_build_query($data);
    }
    $this->options[CURLOPT_POSTFIELDS] = $data;
    return $this;
  }

  public function setOptionAllowRedirect($max_redirects = 3) {
    if($max_redirects == false || $max_redirects == 0) {
      $this->options[CURLOPT_FOLLOWLOCATION] = false;
      $this->options[CURLOPT_MAXREDIRS] = 0;
      return $this;
    }
    $this->options[CURLOPT_FOLLOWLOCATION] = true;
    $this->options[CURLOPT_MAXREDIRS] = $max_redirects;
    return $this;
  }

  /**
   * Resets Requester Options
   */
  public function resetOptions() {
    $this->options = self::$default_options;
    $this->method = 'GET';
    $this->url = '';
  }

}
