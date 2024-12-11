<?php
define('DEBUG', true);
define('VERBOSE', true);
define('QUIET', false);

header("X-Frame-Options: DENY");

if(defined('QUIET') && QUIET === false) {
	ini_set('error_reporting', E_ALL);
	ini_set('display_errors', true);
	ini_set('html_errors', false);
}

session_start();
if(@is_string($_SESSION["csrf_token"]) != true) {
	$string = session_id() . uniqid(); 
	$_SESSION["csrf_token"] = md5($string);
}
define('CSRF_TOKEN', $_SESSION["csrf_token"]);

set_include_path(__DIR__.'/include');
require_once('HTML.php');
$HTML = new HTML();
require_once('get_url_path.php');
require_once('Main.php');
$Main = new Main();

