<?php

/**
 * A Basic query plugin.
 *
 */
require_once 'MuDoCo/Plugin/Interface.php';

class MuDoCo_Plugin_Hello implements MuDoCo_Plugin_Interface {
  
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
   * @param boolean $safe the xss call had correct nonce
   *
   * @see MuDoCO_Server::init()
   * @see $mudoco_conf['MUDOCO_SERVER_INIT']
   */
  public function init($safe) {
    // nothing...
  }
  
  /**
   * The main function.
   *
   * @param array $params
   * @param mixed $data to send back to xssAjaxCallback()
   *
   * @return code some zero or positive code
   *
   * @see MuDoCO_Server::query()
   */
  public function query($params, &$data) {
    $data = (object)array('hello' => 'world ' . date('D, d M Y H:i:s'));
    return 0;
  }
}