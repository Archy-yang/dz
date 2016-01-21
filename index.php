<?php

if(!@include_once './data/config.inc.php') {
	//header('Location: ./install/index.php');
	exit();
}
error_reporting(DEBUG == 0 ? 0 : (DEBUG == 1 ? (E_ERROR | E_WARNING | E_PARSE) : E_ALL));
ini_set('magic_quotes_runtime', 0);

$_GET['m'] = isset($_GET['m']) && in_array($_GET['m'], array('home', 'index', 'champion')) ? $_GET['m'] : 'home';

require_once DIR_MODEL.'/base.model.php';
require_once DIR_CTRL.'/'.$_GET['m'].'.ctrl.php';

$classname = $_GET['m'].'Control';
$control = new $classname();

$_GET = $control->_addslashes($_GET, 1, TRUE);
$_POST = $control->_addslashes($_POST, 1, TRUE);
$_COOKIE = $control->_addslashes($_COOKIE, 1, TRUE);
$_SERVER = $control->_addslashes($_SERVER);
$_FILES = $control->_addslashes($_FILES);
$_REQUEST = $control->_addslashes($_REQUEST, 1, TRUE);

if(!isset($_GET['a'])) {
	$_GET['a'] = '';
}
$method = !empty($_GET['a']) && method_exists($control, 'on'.$_GET['a']) ? 'on'.$_GET['a'] : 'onDefault';
$control->$method();

//$control->debug();

?>
