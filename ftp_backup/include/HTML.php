<?php 

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
		ob_start(function($buffer) {
			$buffer = htmlentities($buffer);
			$buffer = 
////////////////////////////////////////////////////////////////////////////////
<<<HEREDOC
<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="content-type" content="text/html; charset=utf-8" />
		<meta name="viewport" content="width=device-width, height=device-height, initial-scale=1.0" />
	<body><pre>{$buffer}
HEREDOC;
////////////////////////////////////////////////////////////////////////////////
			return($buffer);
		});
	}//}}}
	
	function __wakeup()
	{//{{{
		trigger_error("Can't unserialize this class", E_USER_ERROR);
		exit(255);
	}//}}}
	
	function __destruct()
	{//{{{
		$buffer = ob_get_contents();
		ob_end_clean();
		$buffer = htmlentities($buffer);
		
		HTML::$body = "<pre>{$buffer}</pre>".HTML::$body;
		$html = HTML::generate_html();
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

	static function generate_html()
	{//{{{
		$head = &self::$head;
		$title = &self::$title;
		$stylesheets = self::generate_stylesheets(self::$styles);
		$style = &self::$style;
		$scripts = self::generate_scripts(self::$scripts);
		$script = &self::$script;
		$body = &self::$body;
		$html = 
////////////////////////////////////////////////////////////////////////////////
<<<HEREDOC
<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="content-type" content="text/html; charset=utf-8" />
		<meta name="viewport" content="width=device-width, height=device-height, initial-scale=1.0" />
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
////////////////////////////////////////////////////////////////////////////////
		return($html);
	}//}}}
	
}

