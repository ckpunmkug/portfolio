<?php
define('DEBUG', true);
define('QUIET', false);
define('VERBOSE', false);

header("X-Frame-Options: DENY");

ini_set('error_reporting', E_ALL);
ini_set('display_errors', true);
ini_set('html_errors', false);

set_include_path(__DIR__.'/include');
require_once(__DIR__.'/config.php');
require_once('HTML.php');
$HTML = new HTML();
require_once('FTPBackup.php');
require_once('date_to_timestamp.php');
require_once('main.php');

$return = set_time_limit(0);
if(!$return) {
	trigger_error("Can't set no time limit", E_USER_WARNING);
	return(false);
}

ini_set("ignore_user_abort", true);

if(@strval($_GET["run_type"]) == 'cron') {
	$return = main(__DIR__.'/data');
	if($return === true) {
		exit(0);
	}
	else {
		exit(255);
	}
}
else {
	HTML::$body .= '<a href="index.php?run_type=cron">Запустить</a>';
}

