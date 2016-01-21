<?php

define('DEBUG', 1);

define('DB_HOST', 'localhost');
define('DB_PORT', '27017');
define('DB_USER', '');
define('DB_PW', '');
define('DB_NAME', 'championgg');
define('DB_TBLPRE', 'tablepre');
define('DB_CONNECT', '0');
define('DB_CHARSET', 'utf8');
define('CHARSET', 'uft-8');

define('COOKIE_PATH', '/');
define('COOKIE_DOMAIN', '".$cookiedomain."');
define('COOKIE_PRE', '".random(3)."_');

define('DIR_ROOT', substr(dirname(__FILE__), 0, -4));
define('DIR_ADMIN', DIR_ROOT.'admin');
define('DIR_API', DIR_ROOT.'api');
define('DIR_ATTACH', DIR_ROOT.'attach');
define('DIR_CTRL', DIR_ROOT.'ctrl');
define('DIR_DATA', DIR_ROOT.'data');
define('DIR_IMG', DIR_ROOT.'img');
define('DIR_JS', DIR_ROOT.'js');
define('DIR_LANG', DIR_ROOT.'lang');
define('DIR_LIB', DIR_ROOT.'lib');
define('DIR_MODEL', DIR_ROOT.'model');
define('DIR_TPL', DIR_ROOT.'tpl');
define('DIR_JSMODULE', DIR_ROOT.'module');
define('IN_WP', 1);
define('WAPCONV', 0);
define('ISWAP', 0);
define('MAGIC_QUOTES_GPC', true);
define("CORE_DDPATCH", '6.1.1');
define('APP_NAME', 'core');
