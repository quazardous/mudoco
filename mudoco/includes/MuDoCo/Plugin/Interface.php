<?php

/**
 * MuDoCo Plugin interface
 * 
 * Allow extension of server capability.
 * 
 * @see MuDoCO_Server::query()
 */
interface MuDoCo_Plugin_Interface {
  
  /**
   * The constructor.
   * 
   * @param MuDoCo_Server $server
   */
  public function __construct(MuDoCo_Server $server);
  
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
  public function query($params, &$data);
  
  /**
   * The init function.
   *
   * @param string $mode xss or api
   * @param boolean $safe the XSS call had correct nonce
   *
   * @see MuDoCO_Server::init()
   * @see $mudoco_conf['MUDOCO_SERVER_INIT']
   */
  public function init($mode, $safe);
}