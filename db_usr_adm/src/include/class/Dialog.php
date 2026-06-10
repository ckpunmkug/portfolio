<?php

class Dialog
{
	static $BIN = [
		"whiptail" => '/usr/bin/whiptail',
	];
	
	static function launch(string $arguments)
	{//{{{//
	
		$command = self::$BIN['whiptail'] .' '. $arguments;
		$std = [
			STDIN,
			STDOUT,
			["pipe", "w"],
		];
		$PIPE = [];
		$cwd = getcwd();
		$ENVIRONMENT = getenv();
		$ENVIRONMENT["_"] = self::$BIN["whiptail"];

		$proc = proc_open($command, $std, $PIPE, $cwd, $ENVIRONMENT);
		if(!is_resource($proc)) {
			if(defined('DEBUG') && DEBUG) var_dump(['$command' => $command]);
			trigger_error("Can't open process", E_USER_WARNING);
			return(false);	
		}

		while(true) {
			$proc_status = proc_get_status($proc);
			if(!is_array($proc_status)) {
				trigger_error("Can't get process status for command", E_USER_WARNING);
				return(false);
			}
			if($proc_status["running"] == false) break;
			
			usleep(100000);
		}
		
		$result = [
			"status" => $proc_status["exitcode"],
			"stderr" => stream_get_contents($PIPE[2]),
		];
		
		fclose($PIPE[2]);
		proc_close($proc);
		
		return($result);
		
	}//}}}//

	static function menu(array $ITEM, string $text = '')
	{//{{{//
		
		$text = escapeshellarg($text);
		$arguments = "--menu $text 0 0 0";
		foreach($ITEM as $key => $item) {
			$key = escapeshellarg($key);
			$item = escapeshellarg($item);
			$arguments .= " {$key} {$item}";
		}
		
		$result = self::launch($arguments);
		if(!is_array($result)) {
			trigger_error("Can't launch 'whiptail'", E_USER_ERROR);
			exit(255);
		}
		if($result["status"] != 0 && $result["status"] != 1) {
			trigger_error("'whiptail' exit with error", E_USER_ERROR);
			exit(255);
		}
		
		return($result);
		
	}//}}}//

	static function inputbox(string $text, string $init = '')
	{//{{{//
		
		$text = escapeshellarg($text);
		$init = escapeshellarg($init);
		$arguments = "--inputbox {$text} 0 0 {$init}";
		
		$result = self::launch($arguments);
		if(!is_array($result)) {
			trigger_error("Can't launch 'whiptail'", E_USER_ERROR);
			exit(255);
		}
		if($result["status"] != 0 && $result["status"] != 1) {
			trigger_error("'whiptail' exit with error", E_USER_ERROR);
			exit(255);
		}
		
		return($result);
		
	}//}}}//
	
	static function msgbox(string $text)
	{//{{{//
		
		$text = escapeshellarg($text);
		$arguments = "--msgbox {$text} 0 0";
		
		$result = self::launch($arguments);
		if(!is_array($result)) {
			trigger_error("Can't launch 'whiptail'", E_USER_ERROR);
			exit(255);
		}
		if($result["status"] != 0 && $result["status"] != 1) {
			trigger_error("'whiptail' exit with error", E_USER_ERROR);
			exit(255);
		}
		
		return($result);
		
	}//}}}//

	static function yesno(string $text)
	{//{{{//
		
		$text = escapeshellarg($text);
		$arguments = "--yesno {$text} 0 0";
		
		$result = self::launch($arguments);
		if(!is_array($result)) {
			trigger_error("Can't launch 'whiptail'", E_USER_ERROR);
			exit(255);
		}
		if($result["status"] != 0 && $result["status"] != 1) {
			trigger_error("'whiptail' exit with error", E_USER_ERROR);
			exit(255);
		}
		
		return($result);
		
	}//}}}//

	static function textbox(string $file)
	{//{{{//
		
		$file = escapeshellarg($file);
		$arguments = "--textbox {$file} 0 0";
		
		$result = self::launch($arguments);
		if(!is_array($result)) {
			trigger_error("Can't launch 'whiptail'", E_USER_ERROR);
			exit(255);
		}
		if($result["status"] != 0 && $result["status"] != 1) {
			trigger_error("'whiptail' exit with error", E_USER_ERROR);
			exit(255);
		}
		
		return($result);
		
	}//}}}//
}

