<?php

function get_url_path() // string
{//{{{
	if(@is_string($_SERVER["REQUEST_URI"]) !== true) {
		if(defined('DEBUG') && DEBUG) @var_dump(['$_SERVER' => $_SERVER]);
		trigger_error('Incorrect string $_SERVER["REQUEST_URI"]', E_USER_WARNING);
		return(false);
	}
	
	$url_path = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
	if(!is_string($url_path)) {
		if(defined('DEBUG') && DEBUG) var_dump(['$_SERVER["REQUEST_URI"]' => $_SERVER["REQUEST_URI"]]);
		trigger_error('Parse url failed from $_SERVER["REQUEST_URI"]', E_USER_WARNING);
		return(false);
	}
	
	return($url_path);
}//}}}

