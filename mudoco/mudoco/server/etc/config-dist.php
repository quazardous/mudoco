<?php

$mudoco_conf['MUDOCO_STORAGE_SESSION_SALT'] = 'my-salt-123';
$mudoco_conf['MUDOCO_STORAGE_NONCE_SALT'] = 'my-salt-123';
//$mudoco_conf['MUDOCO_PLUGIN_SESSION_SESSION_STORAGE_CLASS'] = 'MuDoCo_Storage_Session_Default';
//$mudoco_conf['MUDOCO_STORAGE_SESSION_COOKIENAME'] = 'MDCID';
//$mudoco_conf['MUDOCO_STORAGE_SESSION_LIFETIME'] = 365*24*3600;
//$mudoco_conf['MUDOCO_SERVER_NONCE_STORAGE_CLASS'] = 'MuDoCo_Storage_Nonce_Sqlite';
$mudoco_conf['MUDOCO_STORAGE_NONCE_SQLITE_FILE'] = '/path/to/mudoco-nonce.sqlite3'; // file will be created
$mudoco_conf['MUDOCO_SERVER_PLUGINS_DIR'] = '/path/to/mudoco/plugins'; // for testsite we need the heelo plugin

// list of plugins to init at each xss call
$mudoco_conf['MUDOCO_SERVER_INIT'] = array(
    'session', // handle session
    );

$paths = explode(PATH_SEPARATOR, get_include_path());
array_unshift($paths, '../../../includes');
set_include_path(implode(PATH_SEPARATOR, $paths));

