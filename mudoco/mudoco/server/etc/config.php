<?php

$mudoco_conf['MUDOCO_STORAGE_SESSION_SALT'] = 'test-dza46zda';
$mudoco_conf['MUDOCO_STORAGE_NONCE_SALT'] = 'test-dza46zda';
//$mudoco_conf['MUDOCO_STORAGE_SESSION_COOKIENAME'] = 'MDCID';
//$mudoco_conf['MUDOCO_STORAGE_SESSION_LIFETIME'] = 365*24*3600;
$mudoco_conf['MUDOCO_STORAGE_NONCE_SQLITE_FILE'] = '/tmp/mudoco-nonce.sqlite3';
$mudoco_conf['MUDOCO_SERVER_PLUGINS_DIR'] = '/home/berlioz/www/mudoco/plugins';

// list of plugins to init at each xss call
$mudoco_conf['MUDOCO_SERVER_INIT'] = array(
    'cookie', // handle session cookie
    );

$paths = explode(PATH_SEPARATOR, get_include_path());
array_unshift($paths, '../../../includes');
set_include_path(implode(PATH_SEPARATOR, $paths));

