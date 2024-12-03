<?php

header("X-Frame-Options: DENY");

session_start();
if(!defined('CSRF_TOKEN')) {
	if(@is_string($_SESSION["csrf_token"]) != true) {
		$_SESSION["csrf_token"] = md5(session_id());
	}
	
	define('CSRF_TOKEN', $_SESSION["csrf_token"]);
}

if(
	isset($_SERVER["REQUEST_METHOD"])
	&& is_string($_SERVER["REQUEST_METHOD"])
	&& $_SERVER["REQUEST_METHOD"] == 'POST'
) {
	if(!(
		isset($_POST['csrf_token'])
		&& is_string($_POST['csrf_token'])
		&& strcmp(CSRF_TOKEN, $_POST['csrf_token']) === 0
	)) {
		trigger_error("Incorrect or not passed 'csrf_token' in POST request", E_USER_ERROR);
		exit(255);
	}
}

