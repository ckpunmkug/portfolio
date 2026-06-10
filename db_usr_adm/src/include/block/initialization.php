<?php

define('VENDOR', 'ckpunmkug');
$return = basename(DIR);
define('PROJECT', $return);

if(true) {
	$return = getenv('HOME', true);
	if(!is_string($return)) {
		trigger_error("Environment variable 'HOME' is not set", E_USER_ERROR);
		exit(255);
	}
	$return = realpath($return);
	if(!is_string($return)) {
		trigger_error("Unable to get real path of 'HOME' folder", E_USER_ERROR);
		exit(255);
	}
	define('HOME', $return);
}

if(true) {
	$path = HOME.'/.config/'.VENDOR.'/'.PROJECT.'/config.php';
	$return = file_exists($path);
	if($return) {
		require($path);
	}
	else {
		require('project/default.php');
	}
}

//require('class/Check.php');
//require('class/FileSystem.php');
//require('class/SQLite.php');
require('class/MySQL.php');
//require('function/encode.php');
//require('function/decode.php');
//require('function/export.php');
//require('function/import.php');

if(true) {
	require('project/Data.php');
	if(true) {
		$return = Database::open(DATABASE["user"], DATABASE["password"], DATABASE["name"]);
		if(!$return) {
			trigger_error("Can't open database via socket", E_USER_ERROR);
			exit(255);
		}
	}
	if(!true && file_exists(PATH["database"])) {
		$return = Database::open(PATH["database"]);
		if(!$return) {
			trigger_error("Can't open database from file", E_USER_ERROR);
			exit(255);
		}
	}
}

