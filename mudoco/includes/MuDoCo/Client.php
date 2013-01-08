<?php
/**
 * MuDoCo - A Multi Domain Cookie
 * 
 * Client side helper class.
 * 
 * @author berliozdavid@gmail.com
 *
 * NB : this file is standalone and can be put on the client server.
 */
class MuDoCo_Client {
    
  protected function httpRequest($url) {
    // basic HTTP request implementation
    return file_get_contents($url);
  }
  
  protected $_hnonce = null;
  protected function getHashedNonce() {
    if (empty($this->_hnonce)) {
      // client nonce is send to mudoco
      $url = $this->serverBaseUrl() . '/restricted/n.php?';
      $url .= http_build_query(array('a' => $this->getClientNonce()));
      if ($res = $this->httpRequest($url)) {
        // mudoco return md5(cnonce+nonce)
        $this->_hnonce = trim($res);
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
    if (isset($mudoco_conf['MUDOCO_CLIENT_COOKIENAME'])) {
      return $mudoco_conf['MUDOCO_CLIENT_COOKIENAME'];
    }
    return 'MDCL';
  }
  
  protected function serverBaseUrl() {
    global $mudoco_conf;
    if (isset($mudoco_conf['MUDOCO_SERVER_BASE'])) {
      return $mudoco_conf['MUDOCO_SERVER_BASE'];
    }
    return NULL;
  }
  
  protected function salt() {
    global $mudoco_conf;
    if (isset($mudoco_conf['MUDOCO_CLIENT_SALT'])) {
      return $mudoco_conf['MUDOCO_CLIENT_SALT'];
    }
    static $salt;
    if (empty($salt)) $salt = time();
    return $salt;
  }
}