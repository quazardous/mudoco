<?php

/**
 * The default session plugin.
 * Handle centralized session.
 * 
 * Takes :
 *  - k and v : set the given session keys with the given values.

 *  - k : returns the given session keys values.
 *  
 *   '^' can be use as separator for multiple keys and values.
 */
require_once 'MuDoCo/Plugin/Interface.php';
require_once "MuDoCo/Storage/Session/Interface.php";

class MuDoCo_Plugin_Session implements MuDoCo_Plugin_Interface {
  
  /**
   * @var MuDoCo_Server
   */
  protected $_server;
  
  /**
   * The constructor.
   *
   * @param MuDoCo_Server $server
   */
  public function __construct(MuDoCo_Server $server) {
    $this->_server = $server;
  }
  
  /**
   * The init function.
   *
   * @param string $mode xss or api
   * @param boolean $safe the xss call had correct nonce
   *
   * @see MuDoCO_Server::init()
   * @see $mudoco_conf['MUDOCO_SERVER_INIT']
   */
  public function init($mode, $safe) {
    if ($mode == 'xss') {
      $this->getSessionStorage()->cookie();
    }
  }
  
  /**
   * Set or get session data.
   * 
   * @param array $params with keys :
   *  - k : the vakue keys to set
   *  - v : the values
   *  
   *  ^ can be used as separator.
   *  
   * @param mixed $data
   * 
   * @see MuDoCo_Plugin_Interface::query()
   */
  public function query($params, &$data) {
    
    $session = $this->getSessionStorage()->read();
    if (empty($session)) $session = array();
    
    if (isset($params['k'])&&isset($params['v'])) {
      $keys = explode('^', $params['k']);
      $values = explode('^', $params['v']);
      $session = $this->getSessionStorage()->write(array_merge($session, array_combine($keys, $values)));
    }
    if (isset($params['k'])) {
      $keys = explode('^', $params['k']);
      $data = array_intersect_key($session, array_combine($keys, $keys));
    }
    
    return 0;
  }

  /**
   * Session storage adapter
   *
   * @var MuDoCo_Storage_Session_Interface
   */
  protected $_sessionStorage = null;
  
  /**
   * Returns the session storage adapter.
   * Instanciates the default storage adapter.
   *
   * @return MuDoCo_Storage_Session_Interface
   */
  public function getSessionStorage() {
    if (!$this->_sessionStorage) {
      $class = $this->getSessionStorageClass();
      require_once strtr($class, '_', '/') . ".php";
      $this->_sessionStorage = new $class;
    }
    return $this->_sessionStorage;
  }
  
  protected function getSessionStorageClass() {
    global $mudoco_conf;
    if (isset($mudoco_conf['MUDOCO_PLUGIN_SESSION_STORAGE_CLASS'])) {
      return $mudoco_conf['MUDOCO_PLUGIN_SESSION_STORAGE_CLASS'];
    }
    return 'MuDoCo_Storage_Session_Default';
  }
  
}