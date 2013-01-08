<?php

$mudoco_conf['MUDOCO_CLIENT_SALT'] = 'test-dza46zda';
$mudoco_conf['MUDOCO_SERVER_BASE'] = 'https://cookie.loc/berlioz/mudoco/mudoco/server';
//$mudoco_conf['MUDOCO_CLIENT_COOKIENAME'] = 'MDCL';

$paths = explode(PATH_SEPARATOR, get_include_path());
array_unshift($paths, '../../includes');
set_include_path(implode(PATH_SEPARATOR, $paths));

