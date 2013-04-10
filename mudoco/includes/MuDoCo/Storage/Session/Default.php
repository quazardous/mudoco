<?php
/**
 * MuDoCo - A Multi Domain Cookie
 * 
 * Server side classes.
 * 
 * @author berliozdavid@gmail.com
 *
 */

require_once 'MuDoCo/Storage/Session/Interface.php';

/**
 * Basic Session Adapter using PHP session_* functions.
 * 
 */
class MuDoCo_Storage_Session_Default implements MuDoCo_Storage_Session_Interface {

  public function __construct() {
    global $mudoco_conf;
    $mudoco_conf += array(
      'MUDOCO_STORAGE_SESSION_SALT' => time(),   
      'MUDOCO_STORAGE_SESSION_COOKIENAME' => 'MDCID',
      'MUDOCO_STORAGE_SESSION_LIFETIME' => 365*24*3600, // 1 year lifetime
    );
  }
  
  protected function start() {
    $mdcid = $this->id();
    // we do not want the standard PHPSESSID !
    // it's an ugly implementation ...
    ini_set('session.use_cookies', 0);
    ini_set('session.use_only_cookies', 0);
    ini_set('session.use_trans_sid', 1);
    session_id('mudoco-' . $mdcid);
    session_start();
  }
  
  protected function stop() {
    session_write_close();
  }
  
  public function read() {
    $this->start();
    $data = $_SESSION['data'];
    $this->stop();
    return $data;
  }

  public function write($data) {
    $this->start();
    $_SESSION['data'] = $data;
    $_SESSION['time'] = time();
    $data = $_SESSION['data'];
    $this->stop();
    return $data;
  }
  
  public function cookie() {
    $mdcid = $this->id();
    setcookie($this->cookieName(), $mdcid, time() + $this->lifetime(), '/', '', false, true);
  }
  
  public function id() {
    if (!empty($_COOKIE[$this->cookieName()])) {
      return $_COOKIE[$this->cookieName()];
    }
    return md5($this->salt() . time() . rand() . uniqid());
  }
  
  protected function lifetime() {
    global $mudoco_conf;  
    return $mudoco_conf['MUDOCO_STORAGE_SESSION_LIFETIME'];
  }
  
  protected function cookieName() {
    global $mudoco_conf;
    return $mudoco_conf['MUDOCO_STORAGE_SESSION_COOKIENAME'];
  }
  
  protected function salt() {
    global $mudoco_conf;
    return $mudoco_conf['MUDOCO_STORAGE_SESSION_SALT'];
  }
}
