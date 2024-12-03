<?php 

/// Tool for generate standard markup

class HTML
{

	static $head = "";
	static $title = "";
	static $styles = [];
	static $style = "";
	static $body = "";
	static $scripts = [];
	static $script = "";
	
	function __construct()
	{//{{{
	}//}}}
	
	function __wakeup()
	{//{{{
		trigger_error("Can't userialize this class", E_USER_ERROR);
		exit(255);
	}//}}}
	
	function __destruct()
	{//{{{
		$ob_level = ob_get_level();
		if($ob_level > 0) {
			$ob = ob_get_contents();
			ob_end_clean();
		
			if(!( defined('QUIET') && QUIET === true )) {
				$buffer = &$ob;
				
				$buffer_len = strlen($buffer);
				$default_html_len = strlen(DEFAULT_HTML);
				
				$default_html = '';
				if($buffer_len >= $default_html_len) {
					$default_html = substr($buffer, 0, $default_html_len);
				}
				
				if(strcmp(DEFAULT_HTML, $default_html) === 0) {
					$substr = substr($buffer, $default_html_len);
					$buffer = $substr;
				}
	
				if(!empty($ob)) {
					$ob = strip_tags($ob);
					
					$body = '<pre>'. $ob .'</pre>';
					HTML::$body = $body.HTML::$body;
				}
			}
		}
		$html = HTML::generate();
		echo($html);
	}//}}}
	
	static function generate_stylesheets(array $styles) // string
	{//{{{
		$result = "";
		foreach($styles as $style) {
			if(!is_string($style)) continue;
			$result .= 
////////////////////////////////////////////////////////////////////////////////
<<<HEREDOC
<link rel="stylesheet" href="{$style}" />

HEREDOC;
////////////////////////////////////////////////////////////////////////////////
		}
		return($result);
	}//}}}
	
	static function generate_scripts(array $scripts) // string
	{//{{{
		$result = "";
		foreach($scripts as $script) {
			if(!is_string($script)) continue;
			$result .= 
////////////////////////////////////////////////////////////////////////////////
<<<HEREDOC
<script src="{$script}"></script>

HEREDOC;
////////////////////////////////////////////////////////////////////////////////
		}
		return($result);
	}//}}}

	static function get_url_path() // string
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

	static function generate_csrf_input() // string
	{//{{{
		if(!( defined('CSRF_TOKEN') && is_string('CSRF_TOKEN') )) {
			trigger_error("Incorrect CSRF_TOKEN", E_USER_WARNING);
			return('');
		}
		
		$csrf_token = htmlentities(CSRF_TOKEN);
		$input = 
////////////////////////////////////////////////////////////////////////////////
<<<HEREDOC
<input name="csrf_token" value="{$csrf_token}" type="hidden" />

HEREDOC;
////////////////////////////////////////////////////////////////////////////////
		return($input);
	}//}}}

	static function generate()
	{//{{{
		$head = &self::$head;
		$title = &self::$title;
		$stylesheets = self::generate_stylesheets(self::$styles);
		$style = &self::$style;
		$body = &self::$body;
		$scripts = self::generate_scripts(self::$scripts);
		$script = &self::$script;
		$html = 
<<<HEREDOC
<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="content-type" content="text/html; charset=utf-8" />
		<meta name="viewport" content="width=device-width, height=device-height, minimum-scale=1.0, initial-scale=1.0" />
{$head}
		<title>{$title}</title>
{$stylesheets}
		<style>
{$style}
		</style>
{$scripts}
		<script>
{$script}
		</script>
	</head>
	<body>
{$body}
	</body>
</html>
HEREDOC;
		return($html);
	}//}}}
	
}

