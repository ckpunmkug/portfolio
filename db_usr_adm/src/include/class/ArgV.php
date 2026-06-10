<?php

// Usage
/* {{{
	ArgV::$description = "Program description";
	ArgV::add([
		"-a", "--A", NULL, "not required parameter",
		function () {
			define("A", true);
		}, false
	]);
	ArgV::add([
		"-b", "--B", NULL, "required parameter",
		function () {
			define("B", true);
		}, true
	]);
	ArgV::add([
		"-c", "--C", "STRING", "not required parameter with value",
		function ($string) {
			define("C", $string);
		}, false
	]);
	ArgV::add([
		"-d", "--D", "STRING", "required parameter with value",
		function ($string) {
			define("D", $string);
		}, true
	]);
	ArgV::add([
		"-first", NULL, NULL, "middle name not required parameter",
		function () {
			define("FIRST", $string);
		}, false
	]);
	ArgV::add([
		NULL, "--second", "STRING", "middle name required parameter with value",
		function ($string) {
			define("SECOND", $string);
		}, true
	]);
	ArgV::add([
		"--without-description", NULL, NULL, NULL,
		function ($string) {
			define("SECOND", $string);
		}, true
	]);
	ArgV::apply();
}}} */

class ArgV
{

	static $description = "";
	static $config = [];
	
	static function help()
	{//{{{
	
		$text = "";
		
		if (strlen(self::$description) > 0) {
			$string = rtrim(self::$description);
			$text .= "\nDescription:\n{$string}\n\n";
		}
			
		$text .= "Parameters: \n";
		foreach (self::$config as $config) {
			$text .= "\n";
			
			if(!empty($config[0]))
				$text .= "  {$config[0]}";
			
			if(!empty($config[1]))
				$text .= "  {$config[1]}";
			
			if(!empty($config[2]))
				$text .= " {$config[2]}";
			
			if(!empty($config[3]))
				$text .= "\n        {$config[3]}";
			
			$text .= "\n";
		}
		
		echo($text."\n");
		
		return(true);
		
	}//}}}
	
	static function apply()
	{//{{{
	
		self::add();
		
		global $argv;
		array_walk(
			self::$config, 
			function(array $config, int $index, array $argv) {
				$c = count($argv);
				for ($i = 1; $i < $c; $i++) {
					if(!( $argv[$i] == $config[0] || $argv[$i] == $config[1] )) continue;
					
					if (!empty($config[2])) {
						if (!isset($argv[($i+1)])) {
							trigger_error("'{$config[2]}' is not set for '{$config[3]}' in command line", E_USER_ERROR);
							exit(255);
						}
						self::$config[$index][4]($argv[$i+1]);
						return null;
					}
					else {
						self::$config[$index][4]();
						return null;
					}
				}
				if (self::$config[$index][5]) {
					trigger_error("'{$config[3]}' is not set in command line", E_USER_ERROR);
					exit(255);
				}
			}, 
			$argv
		);
		
		return(true);
		
	}//}}}
	
	static function add(array $config = [])
	{//{{{
	
		if (!empty($config)) {
			array_push(self::$config, $config);
			return(true);
		}
		
		self::add([
			'-v', '--verbose', null, "Allow verbose messages to stderr", 
			function() {
				define('VERBOSE', true);
			}, false
		]);
		
		self::add([
			'-q', '--quiet', null, "Prevent output to stdout and error reporting", 
			function() {
				define('QUIET', true);
				ob_start();
				register_shutdown_function(function () {
					ob_end_clean();
				} );
				error_reporting(0);
			}, false
		]);
		
		self::add([
			'-d', '--debug', null, "Run in debug mode", 
			function() {
				define('DEBUG', true);
			}, false
		]);
		
		array_unshift(self::$config, [
			'-h', '--help', null, "Show help text and exit",
			function() {
				self::help();
				exit(0);
			}, false
		]);
		
		return(true);
		
	}//}}}
	
}

