<?php
/**
 * MuDoCo - A Multi Domain Cookie
 *
 * Server side public xss script.
 *
 * @param a cnonce|hnonce
 * @param q query plugin
 * @param r random
 * @param i xss context
 * 
 * hnonce = md5(cnonce nonce)
 *
 */

include_once __DIR__.'/../etc/config.php';
include_once 'MuDoCo/Server.php';

$server = new MuDoCo_Server;

$data = null;
// negative code means nonce failure
$code = -1;
$init = false;

if (isset($_GET['a'])) {
  list($cnonce, $hnonce) = explode ('|', $_GET['a']);
  if($server->checkNonce($cnonce, $hnonce)) {
    $code = 0;
    $server->init('xss', true); $init = true;
    if (isset($_GET['q'])) {
      $params = array_diff_key($_GET, array('a'=>'', 'i'=>'', 'r'=>'', 'q'=>''));
      $plugin = $server->getPlugin($_GET['q']);
      $plugin->init('xss', true);
      $code = $plugin->query(array_diff_key($_GET, array('q'=>'')), $data);
    }
  }
}

if (!$init) {
  $server->init('xss');
}

$server->xss($data, $code, isset($_GET['i']) ? $_GET['i'] : null);