<?php
/**
 * MuDoCo - A Multi Domain Cookie
 *
 * Server side public xss script.
 *
 * @param _a cnonce|hnonce
 * @param _q query plugin
 * @param _r random
 * @param _m variable name
 * @param _i xss context
 * 
 * hnonce = md5(cnonce nonce)
 *
 */

include_once __DIR__.'/../etc/config.php';
include_once 'MuDoCo/Server.php';

$server = new MuDoCo_Server(isset($_GET['_m']) ? $_GET['_m'] : null);

$data = null;
// negative code means nonce failure
$code = -1;
$init = false;

if (isset($_GET['_a'])) {
  list($cnonce, $hnonce) = explode ('|', $_GET['_a']);
  if($server->checkNonce($cnonce, $hnonce)) {
    $code = 0;
    $server->init('xss', true); $init = true;
    if (isset($_GET['_q'])) {
      $params = array_diff_key($_GET, array('_a'=>'', '_i'=>'', '_r'=>'', '_q'=>''));
      $plugin = $server->getPlugin($_GET['_q']);
      $plugin->init('xss', true);
      $code = $plugin->query(array_diff_key($_GET, array('_q'=>'')), $data);
    }
  }
}

if (!$init) {
  $server->init('xss');
}

$server->xss($data, $code, isset($_GET['_i']) ? $_GET['_i'] : null);