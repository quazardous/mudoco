<?php

$mudoco_conf['MUDOCO_CLIENT_SALT'] = 'my-salt-123';
$mudoco_conf['MUDOCO_SERVER_BASE'] = 'https://cookie.dom/mudoco/server'; // should point on mudoco/server/
//$mudoco_conf['MUDOCO_CLIENT_COOKIENAME'] = 'MDCL';

$paths = explode(PATH_SEPARATOR, get_include_path());
array_unshift($paths, '../../includes');
set_include_path(implode(PATH_SEPARATOR, $paths));

