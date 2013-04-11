<?php
/**
 * MuDoCo - A Multi Domain Cookie
 * 
 * Server side API webservice.
 * 
 * 
 * System call
 * param _s : system call name
 * 
 *  - nonce : Generate a nonce and register it with the given client nonce.
 *   - param cn : client nonce
 * 
 * Query plugin
 * param q : plugin tag
 *   calls the query function of the given plugin
 * 
 * 
 * Returns : json
 *  {
 *    code: int,
 *    data: mixed,
 *  }
 * 
 * 
 * NB : This file should be IP protected.
 * 
 */

include_once __DIR__.'/../etc/config.php';
include_once 'MuDoCo/Server.php';

$server = new MuDoCo_Server;

$server->init('api');

$code = -1; // >=0 for success or custom codes
$data = null;
if (isset($_GET['_s'])) {
  // system call
  $code = $server->apiSystem($_GET['_s'], array_diff_key($_GET, array('_s'=>'')), $data);
}
elseif (isset($_GET['_q'])) {
  // plugin call
  $plugin = $server->getPlugin($_GET['_q']);
  $plugin->init('api');
  $code = $plugin->query(array_diff_key($_GET, array('_q'=>'')), $data);
}

$server->api($data, $code);
