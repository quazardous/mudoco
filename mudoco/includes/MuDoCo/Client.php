<?php
/**
 * MuDoCo - A Multi Domain Cookie
 * 
 * Client side helper class.
 * 
 * @author berliozdavid@gmail.com
 *
 * NB : this file is nearly standalone and can be put on the client server.
 */

require_once 'webfingerprint.class.php';

class MuDoCo_Client {
  
  public function __construct() {
    global $mudoco_conf;
    $mudoco_conf += array(
      'MUDOCO_CLIENT_SALT' => time(),
      'MUDOCO_SERVER_BASE' => null, // should point on mudoco/server/
      'MUDOCO_CLIENT_COOKIENAME' => 'MDCL',
    );
  }
  
  static protected function get_client_ip() {
    foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $key) {
    if (array_key_exists($key, $_SERVER) === true) {
      foreach (explode(',', $_SERVER[$key]) as $ip) {
          if (filter_var($ip, FILTER_VALIDATE_IP) !== false) {
            return $ip;
          }
        }
      }
    }
    return null;
  }	
	
  static public function get_finger_print() {
  	$fp = '';
  	$fp .= (isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '') . ':'; 
  	$fp .= (isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '') . ':'; 
  	$fp .= self::get_client_ip() . ':';
  	return md5($fp);
  }	
	
  protected function httpRequest($url) {
    // basic HTTP request implementation
    return file_get_contents($url);
  }
  
  protected $_hnonce = null;
  protected function getHashedNonce() {
    if (empty($this->_hnonce)) {
      // client nonce is send to mudoco
      $url = $this->serverBaseUrl() . '/restricted/api.php?';
      $url .= http_build_query(array(
          's' => 'nonce',
          'cn' => $this->getClientNonce(),
      	  'fp' => self::get_finger_print(),
          ));
      if ($res = $this->httpRequest($url)) {
        $res = json_decode($res);
        if ($res->code == 0) {
          // mudoco return md5(cnonce+nonce)
          $this->_hnonce = $res->data;
        }
      }
    }
    return $this->_hnonce;
  }
  
  protected $_clientNonce = null;
  protected function getClientNonce() {
    if (empty($this->_clientNonce)) {
      $this->_clientNonce = md5($this->salt() . time() . rand() . uniqid());
    }
    return $this->_clientNonce;
  }
  
  public function cookieAndBeacon() {
    
    $cnonce = $this->getClientNonce();
    
    // mudoco server returns md5(cnonce + nonce)
    $hnonce = $this->getHashedNonce();
    
    $data = array();
    $data[] = $cnonce;
    $data[] = $hnonce;
    
    // cookie must be javascript readable...
    setcookie($this->cookieName(), implode('|', $data), 0, '/');
 
    header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
    header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
    header('Content-Type: image/gif');
    //header('HTTP/1.0 204 No Content');
    //header('Content-Length: 0', true);
    echo base64_decode('R0lGODlhAQABAJAAAP8AAAAAACH5BAUQAAAALAAAAAABAAEAAAICBAEAOw==');
    
    flush();
  }
  
  protected function cookieName() {
    global $mudoco_conf;
    return $mudoco_conf['MUDOCO_CLIENT_COOKIENAME'];
  }
  
  protected function serverBaseUrl() {
    global $mudoco_conf;
    return $mudoco_conf['MUDOCO_SERVER_BASE'];
  }
  
  protected function salt() {
    global $mudoco_conf;
    return $mudoco_conf['MUDOCO_CLIENT_SALT'];
  }
}