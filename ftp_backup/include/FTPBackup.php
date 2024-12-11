<?php

class FTPBackup
{
	
	static $use_statuses = ['pending', 'processing', 'on-hold'];
	static $use_dates = ['date_created', 'date_modified', 'date_paid'];

	public $ftp = [
		"source" => NULL,
		"destination" => NULL,
	];

	/// WooCommerce getting orders
	
	static function get_orders(array $config)
	{//{{{//
		$url = 
			strval($config["url"])
			.'?consumer_key='.strval($config["key"])
			.'&consumer_secret='.strval($config["secret"])
		;
		$json = self::http_get($url);
		if(!is_string($json)) {
			trigger_error("Can't perform WooCommerce REST API request", E_USER_WARNING);
			return(false);
		}
		
		$orders = self::json_decode($json);
		if(!is_array($orders)) {
			trigger_error("Can't json decode WooCommerce REST API response", E_USER_WARNING);
			return(false);
		}
		
		return($orders);
	}//}}}//

	static function http_get(string $url)
	{//{{{//
		$scheme = parse_url($url, PHP_URL_SCHEME);
		if(!is_string($scheme)) {
			trigger_error("Can't parse url scheme for http get request", E_USER_WARNING);
			return(false);
		}
		if(!($scheme == 'http' || $scheme == 'https')) {
			trigger_error("Incorrect url scheme for http get request", E_USER_WARNING);
			return(false);
		}
		
		$context_options = [
			"http" => [
				"method" => 'GET',
				"header" => [],
				"user_agent" => 'ftp backup script',
				"follow_location" => 1,
				"max_redirects" => 10,
				"protocol_version" => 1.1,
				"timeout" => 30,
				"ignore_errors" => true,
			],
		];
		$context = stream_context_create($context_options);
		if(!is_resource($context)) {
			trigger_error("Can't create context for http get request", E_USER_WARNING);
			return(false);
		}
		
		$stream = fopen($url, 'r', false, $context);
		if(!is_resource($stream)) {
			trigger_error("Can't open url for http get request", E_USER_WARNING);
			return(false);
		}
			
		$contents = stream_get_contents($stream);
		if(!is_string($contents)) {
			trigger_error("Can't get contents from stream in http get request", E_USER_WARNING);
			return(false);
		}
		
		fclose($stream);
		
		return($contents);
	}//}}}//
	
	static function json_decode(string $json)
	{//{{{//
		$variable = json_decode($json, true);
		$error = json_last_error();
		if($variable === NULL && $error !== JSON_ERROR_NONE) {
			$error_msg = json_last_error_msg();
			trigger_error("JSON {$error_msg}", E_USER_WARNING);
			return(false);
		}
		
		return($variable);
	}//}}}//
	
	static function filter_orders(array $orders)
	{//{{{//
		$result = [];
		foreach($orders as $order) {
			$status = strval($order["status"]);
			if(!in_array($status, self::$use_statuses)) continue;
			array_push($result, $order);
		}
		
		return($result);
	}//}}}//
	
	static function get_orders_dates(array $orders)
	{//{{{//
		$result = [];
		
		foreach($orders as $order) {
			foreach(self::$use_dates as $date) {
				$value = strval($order[$date]);
				$expression = '/^20(\d+)\-(\d+)\-(\d+)T.+$/';
				if(preg_match($expression, $value, $MATCH) != 1) continue;
				
				$day = intval($MATCH[3]);
				$month = intval($MATCH[2]);
				$year = intval($MATCH[1]);
				
				$string = sprintf("%d.%d.%d", $day, $month, $year);
				if(!in_array($string, $result)) {
					array_push($result, $string);
				}
			}
		}
		
		return($result);
	}//}}}//
	
	
	/// Open, close connections to source and destination ftp servers

	public function __construct(array $source_ftp, array $destination_ftp)
	{//{{{//
		$parameters = $this->check_ftp_parameters($source_ftp);
		if(!is_array($parameters)) {
			trigger_error("Check source ftp parameters failed", E_USER_ERROR);
		}
		
		$return = ftp_ssl_connect($parameters["host"]);
		if(!is_object($return)) {
			trigger_error("Can't open ssl ftp connection to source server", E_USER_ERROR);
		}
		$this->ftp["source"] = $return;
		
		$return = ftp_login($this->ftp["source"], $parameters["user"], $parameters["password"]);
		if(!$return) {
			trigger_error("Can't login to source ftp", E_USER_ERROR);
		}
		
		ftp_pasv($this->ftp["source"], true);
		
		
		$parameters = $this->check_ftp_parameters($destination_ftp);
		if(!is_array($parameters)) {
			trigger_error("Check destination ftp parameters failed", E_USER_ERROR);
		}
		
		$return = ftp_ssl_connect($parameters["host"]);
		if(!is_object($return)) {
			trigger_error("Can't open ssl ftp connection to destination server", E_USER_ERROR);
		}
		$this->ftp["destination"] = $return;
		
		$return = ftp_login($this->ftp["destination"], $parameters["user"], $parameters["password"]);
		if(!$return) {
			trigger_error("Can't login to destination ftp", E_USER_ERROR);
		}
		
		ftp_pasv($this->ftp["destination"], true);
		
		return(NULL);
	}//}}}//
	
	public function __wakeup()
	{//{{{
		trigger_error("Can't unserialize this class", E_USER_ERROR);
	}//}}}
	
	public function __destruct()
	{//{{{//
		if(!is_null($this->ftp["source"])) {
			@ftp_close($this->ftp["source"]);
		}
		if(!is_null($this->ftp["destination"])) {
			@ftp_close($this->ftp["destination"]);
		}
		return(NULL);
	}//}}}//
	
	public function check_ftp_parameters(array $parameters) // array
	{//{{{//
		$result = [];
		
		if(@is_string($parameters["host"]) !== true) {
			if (defined('DEBUG') && DEBUG) var_dump(['$parameters' => $parameters]);
			trigger_error("Incorrect ftp connection parameter 'host'", E_USER_WARNING);
			return(false);
		}
		$result["host"] = $parameters["host"];
		
		if(@is_string($parameters["user"]) !== true) {
			if (defined('DEBUG') && DEBUG) var_dump(['$parameters' => $parameters]);
			trigger_error("Incorrect ftp connection parameter 'user'", E_USER_WARNING);
			return(false);
		}
		$result["user"] = $parameters["user"];
		
		if(@is_string($parameters["password"]) !== true) {
			if (defined('DEBUG') && DEBUG) var_dump(['$parameters' => $parameters]);
			trigger_error("Incorrect ftp connection parameter 'password'", E_USER_WARNING);
			return(false);
		}
		$result["password"] = $parameters["password"];
		
		return($result);
	}//}}}//

	public function get_files(string $name) // array
	{//{{{
		switch($name) {
			case('source'):
				$ftp = $this->ftp["source"];
				break;
			case('destination'):
				$ftp = $this->ftp["destination"];
				break;
			default:
				trigger_error("Incorrect ftp name", E_USER_WARNING);
				return(false);
		}
		
		$pwd = ftp_pwd($ftp);
		if(!is_string($pwd)) {
			trigger_error("Can't get working directory from source ftp", E_USER_WARNING);
			return(false);
		}
		$dir = rtrim($pwd, '/').'/';

		ftp_pasv($ftp, true);

		$rawlist = ftp_rawlist($ftp, $pwd, true);
		if(!is_array($rawlist)) {
			trigger_error("Can't get raw files list from ftp", E_USER_WARNING);
			return(false);
		}
		var_dump($rawlist);
		return([1,2]);
		
		$FILE = [];
		$count = count($rawlist);
		foreach($rawlist as $rawstring) {
			$count -= 1;
			if($count < 0) break;
			if(defined('VERBOSE') && VERBOSE) {
				echo("\r {$count}     \r");
			}
			
			$expression = 
				'/^'
				.'([\-dl])'
				.'([\-rwxstST]+)'
				.'\s+(\d+)'
				.'\s+(\d+)'
				.'\s+(\d+)'
				.'\s+(\d+)'
				.'\s+(\w+\s+\d+\s+[\d:]+)'
				.'\s(.+)'
				.'$/'
			;
			if(preg_match($expression, $rawstring, $MATCH) != 1) continue;
			
			$file["type"] = $MATCH[1];
			$file["mode"] = $MATCH[2];
			$file["items"] = $MATCH[3];
			$file["uid"] = $MATCH[4];
			$file["gid"] = $MATCH[5];
			$file["size"] = $MATCH[6];
			$file["date"] = $MATCH[7];
			$file["name"] = $MATCH[8];
				
			if(!(
				$file["type"] == '-' 
				&& preg_match('/^[a-f0-9]+_[a-f0-9]+.+$/', $file["name"]) == 1
			)) continue;
			
			$path = "{$dir}{$file["name"]}";
			$time = ftp_mdtm($ftp, $path);
			if(!is_int($time)) {
				trigger_error("Can't get timestamp for file in source ftp", E_USER_WARNING);
				return(false);
			}
			
			array_push($FILE, [
				"time" => $time,
				"path" => $path,
			]);
		}
		
		return($FILE);
	}//}}}

	public function get_paths($ftp)
	{//{{{//
		$result = [];
	
		$pwd = ftp_pwd($ftp);
		if(!is_string($pwd)) {
			trigger_error("Can't get working directory from source ftp", E_USER_WARNING);
			return(false);
		}
		
		$paths = [$pwd];
		for($index = 0; $index < count($paths); $index++) {
			$path = $paths[$index];
			if(preg_match('/^.*\/$/', $path) != 1) continue;
			
			$rawlist = @ftp_rawlist($ftp, $path, true);
			if(!is_array($rawlist)) {
				if (defined('DEBUG') && DEBUG) { 
					var_dump(['' => $path]);
					trigger_error("Can't get raw files list from ftp", E_USER_WARNING);
				}
				continue;
			}
			
			$files = $this->parse_rawlist($rawlist);
			
			foreach($files as $file) {
				if($file["name"] == '.' || $file["name"] == '..') continue;
				
				if($file["type"] == 'd') {
					$string = $path.$file["name"].'/';
					array_push($paths, $string);
					continue;
				}
				
				$string = $path.$file["name"];
				array_push($paths, $string);
					
				if($file["type"] == '-' && preg_match('/^[a-f0-9]+_[a-f0-9]+.+$/', $file["name"]) == 1) {
					array_push($result, $string);
				}
			}
		}
		
		return($result);
	}//}}}//
	
	public function parse_rawlist(array $rawlist)
	{//{{{//
		$files = [];
		
		foreach($rawlist as $rawstring) {
			$expression = 
				'/^'
				.'([\-dl])'
				.'([\-rwxstST]+)'
				.'\s+(\d+)'
				.'\s+([0-9a-zA-Z_\-]+)'
				.'\s+([0-9a-zA-Z_\-]+)'
				.'\s+(\d+)'
				.'\s+(\w+\s+\d+\s+[\d:]+)'
				.'\s(.+)'
				.'$/'
			;
			if(preg_match($expression, $rawstring, $MATCH) != 1) continue;
			
			$file["type"] = $MATCH[1];
			$file["mode"] = $MATCH[2];
			$file["N0"] = $MATCH[3];
			$file["user"] = $MATCH[4];
			$file["group"] = $MATCH[5];
			$file["N1"] = $MATCH[6];
			$file["date"] = $MATCH[7];
			$file["name"] = $MATCH[8];
			
			array_push($files, $file);
		}
		
		return($files);
	}//}}}//

	public function get_timestamps($ftp, $files)
	{//{{{//
		$result = [];
		$cd = count($files);
		foreach($files as $file) {
			$cd -= 1;
			if($cd < 0) break;
			
			if(defined('VERBOSE') && VERBOSE) {
				echo("\r Left: {$cd}      \r");
			}

			$time = @ftp_mdtm($ftp, $file);
			if(!is_int($time)) {
				if (defined('DEBUG') && DEBUG) {
					var_dump(['$file' => $file]);
					trigger_error("Can't get timestamp for file in ftp", E_USER_WARNING);
				}
				continue;
			}
			
			array_push($result, [
				"timestamp" => $time,
				"path" => $file,
			]);	
		}
		return($result);
	}//}}}//
	
	static function exclude_files(array $files, array $dates)
	{//{{{//
		$result = [];
		
		$array = [];
		foreach($dates as $date) {
			$timestamp = date_to_timestamp($date);
			if(!is_int($timestamp)) continue;
			
			array_push($array, [$timestamp, $timestamp+ONE_DAY]);
		}
		$dates = $array;
		
		foreach($files as  $file) {
			$flag = true;
			foreach($dates as $date) {
				if($file["timestamp"] >= $date[0] && $file["timestamp"] <= $date[1]) {
					$flag = false;
				}
			}
			
			if($flag) {
				array_push($result, $file['path']);
			}
		}
		
		return($result);
	}//}}}//
	
	public function move_file(string $file, string $dir)
	{//{{{//
		$basename = basename($file);
		$tempnam = tempnam('/tmp', 'ftp_');
		
		$return = ftp_get($this->ftp["source"], $tempnam, $file);
		if(!$return) {
			trigger_error("Can't get file from source ftp", E_USER_WARNING);
			return(false);
		}
		
		$dir = rtrim($dir, '/');
		$return = ftp_put($this->ftp["destination"], "{$dir}/{$basename}", $tempnam);
		if(!$return) {
			trigger_error("Can't put file to destiantion ftp", E_USER_WARNING);
			return(false);
		}
		
		$return = unlink($tempnam);
		if(!$return) {
			trigger_error("Can't unlink temp file", E_USER_WARNING);
			return(false);
		}
		
		$return = ftp_delete($this->ftp["source"], $file);
		if(!$return) {
			trigger_error("Can't delete file from source ftp", E_USER_WARNING);
			return(false);
		}
		
		return(true);
	}//}}}//
	
	
}

