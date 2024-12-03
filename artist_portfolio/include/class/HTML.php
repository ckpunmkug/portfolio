<?php

class HTML
{
	static $head =
<<<HEREDOC
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
HEREDOC;
	static $title = "";
	static $style = "";
	static $body = "";
	static $script = "";

	static function generate()
	{//{{{
		$head = &self::$head;
		$title = &self::$title;
		$style = &self::$style;
		$body = &self::$body;
		$script = &self::$script;
		$html = 
<<<HEREDOC
<!DOCTYPE html>
<html>
	<head>
{$head}
		<title>{$title}</title>
		<style>
{$style}
		</style>
		<script>
{$script}
		</script>
	</head>
	<body>
{$body}
	</body>
</html>
HEREDOC;
		return $html;
	}//}}}

}

