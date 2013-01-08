<?php
/**
 * MuDoCo - A Multi Domain Cookie
 * 
 * Server side nonce script.
 * 
 * Generate a nonce and register it with the given client nonce.
 * - param a : client nonce.
 * 
 * Returns mixed hashed nonce : hnonce = md5(cnonce + nonce)
 * 
 * This file should be IP protected.
 * 
 */

include_once '../etc/config.php';
include_once 'MuDoCo/Server.php';

if (isset($_GET['a'])) {
  $cnonce = $_GET['a'];
  $server = new MuDoCo_Server;
  $nonce = $server->generateNonce($cnonce);
  echo md5($cnonce . $nonce);
}