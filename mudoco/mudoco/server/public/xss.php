<?php
/**
 * MuDoCo - A Multi Domain Cookie
 *
 * Server side public xss script.
 *
 * @param a cnonce|hnonce
 * @param q query plugin
 * 
 * hnonce = md5(cnonce nonce)
 *
 */

include_once '../etc/config.php';
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
    $server->init(true); $init = true;
    if (isset($_GET['q'])) {
      $q = $_GET['q'];
      $params = array_diff_key($_GET, array('a'=>'', 'i'=>'', 'r'=>'', 'q'=>''));
      $code = $server->query($q, $params, $data);
    }
  }
}

if (!$init) {
  $server->init();
}

$server->xss($data, $code, isset($_GET['i']) ? $_GET['i'] : null);