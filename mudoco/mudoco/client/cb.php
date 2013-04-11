<?php
/**
 * MuDoCo - A Multi Domain Cookie
 * 
 * Client side server beacon script.
 * 
 * It will set a cookie in the client domain with a client nonce and the hashed server nonce.
 * This hashed nonce is then used from the user navigator to authenticate MuDoCo requests.
 * 
 * @author berliozdavid@gmail.com
 * 
 */ 

include_once __DIR__.'/etc/config.php';

include_once 'MuDoCo/Client.php';

$client = new MuDoCo_Client;

$client->cookieAndBeacon();