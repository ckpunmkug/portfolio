<?php 

/// Development messages initialization

if(true) // DEBUG, VERBOSE, QUIET
{//{{{
	if(isset($_GET["debug"])) {
		define('DEBUG', true);
	}
	if(isset($_GET["verbose"])) {
		define('VERBOSE', true);
	}
	if(isset($_GET["quiet"])) {
		define('QUIET', true);
	}

	if(defined('QUIET') && QUIET === true) {
		ini_set('error_reporting', 0);
		ini_set('display_errors', '0');
	}
	else {
		ini_set('error_reporting', E_ALL);
		ini_set('display_errors', '1');
		ini_set('html_errors', '0');
	}
}//}}}

if(true) // DEFAULT_HTML
{//{{{
	$string = 
////////////////////////////////////////////////////////////////////////////////
<<<'HEREDOC'
<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	</head>
	<body>
<pre>
HEREDOC;
////////////////////////////////////////////////////////////////////////////////

	define('DEFAULT_HTML', $string);
	unset($string);

	ob_start(function($buffer) {
		$buffer_len = strlen($buffer);
		$default_html_len = strlen(DEFAULT_HTML);
		
		$default_html = '';
		if($buffer_len >= $default_html_len) {
			$default_html = substr($buffer, 0, $default_html_len);
		}
		
		if(strcmp(DEFAULT_HTML, $default_html) === 0) {
			$substr = substr($buffer, $default_html_len);
			$buffer = DEFAULT_HTML.htmlentities($substr);
			return($buffer);
		}
		else {
			$buffer = htmlentities($buffer);
			return($buffer);
		}
	});

	echo(DEFAULT_HTML);
}//}}}

