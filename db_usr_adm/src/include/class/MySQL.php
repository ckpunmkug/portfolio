<?php

class Database
{
	static $unix_socket = '/run/mysqld/mysqld.sock'; // mysql --print-defaults
	static $OPTION = [
	    PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING,
	    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
	    PDO::ATTR_EMULATE_PREPARES => false,
	];
	
	static $PDO = NULL;
	
	static function open(string $user, string $password = '', string $dbname = '')
	{//{{{//
	
		if($dbname != '') {
			$dsn = 'mysql:unix_socket='. self::$unix_socket .';dbname='. $dbname .';charset=utf8';
		}
		else {
			$dsn = 'mysql:unix_socket='. self::$unix_socket .';charset=utf8';
		}
	
		try {
			self::$PDO = new PDO($dsn, $user, $password, self::$OPTION);
		}
		catch (PDOException $Exception) {
			trigger_error($Exception->getMessage(), E_USER_WARNING);
			return(false);
		}
		
		return(true);		
		
	}//}}}//

	static function query(string $sql, array $data = []) // (bool) true || (array) $ROW || (int) $id
	{//{{{//
		
		$PDOStatement = self::$PDO->prepare($sql);
		if(!is_object($PDOStatement)) {
			if(defined('DEBUG') && DEBUG) var_dump(['$sql' => $sql]);
			trigger_error("Can't prepare sql query", E_USER_WARNING);
			return(false);
		}
		
		foreach($data as $key => $value) {
			if(!(
				is_string($key)
				&& preg_match('/^[_a-zA-Z0-9]+$/', $key) === 1
			)) {
				if(defined('DEBUG') && DEBUG) var_dump(['$key' => $key]);
				trigger_error("Incorrect key of data item", E_USER_WARNING);
				return(false);
			}
			
			$type = gettype($value);
			switch($type) {
				case('integer'):
					$type = PDO::PARAM_INT;
					break;
				case('double'):
					$type = PDO::PARAM_STR;
					break;
				case('string'):
					$type = PDO::PARAM_LOB;
					break;
				default:
					if(defined('DEBUG') && DEBUG) var_dump(['$value' => $value]);
					trigger_error("Unsupported type of data item value", E_USER_WARNING);
					return(false);
			}
			
			$return = $PDOStatement->bindValue(":{$key}", $value, $type);
			if(!$return) {
				if(defined('DEBUG') && DEBUG) var_dump([
					':{$key}' => ":{$key}",
					'$value' => $value,
				]);
				trigger_error("Can't bind value to statement variable", E_USER_WARNING);
				return(false);
			}
		}
		
		$return = $PDOStatement->execute();
		if(!$return) {
			if(defined('DEBUG') && DEBUG) var_dump(['$sql' => $sql]);
			trigger_error("Can't execute a prepared statement", E_USER_WARNING);
			return(false);
		}
		
		$result = true;
		$sql = trim($sql);
		
		$pattern = '/^(select|show)\s+.+$/i';
		$return = preg_match($pattern, $sql);
		if($return == 1) {
			$ROW = $PDOStatement->fetchAll();
			if(!is_array($ROW)) {
				trigger_error("Can't fetch all rows", E_USER_WARNING);
				return(false);
			}
			$result = $ROW;
		}
		
		$pattern = '/^(insert)\s+.+$/i';
		$return = preg_match($pattern, $sql);
		if($return == 1) {
			$id = self::$PDO->lastInsertId();
			$result = $id;
		}
		
		$PDOStatement->closeCursor();
		
		return($result);
		
	}//}}}//

	static function check(string $type, string $string)
	{//{{{//
		
		switch($type) {
			case("database"):
			$max_string_length = 0xFF;
			$pattern = '/^[_0-9a-zA-Z]+$/';
			break;
			
			case("user"):
			$max_string_length = 0xFF;
			$pattern = '/^[\-_0-9a-zA-Z]+$/';
			break;
			
			case("password"):
			$max_string_length = 0xFF;
			$pattern = '/^[^\x00-\x1F\'\\\]+$/';
			break;
			
			case("table"):
			$max_string_length = 0xFF;
			$pattern = '/^[^\x00-\x1F`\\\]+$/';
			break;
			
			default:
			if(defined('DEBUG') && DEBUG) var_dump(['$type' => $type]);
			trigger_error("Incorrect check type", E_USER_WARNING);
			return(false);
		}
		
		$return = strlen($string);
		if($return > $max_string_length) {
			trigger_error("'{$type}' string length above maximum", E_USER_WARNING);
			return(false);	
		}
		
		$return = preg_match($pattern, $string);
		if($return !== 1) {
			trigger_error("Incorrect char in '{$type}' string", E_USER_WARNING);
			return(false);
		}
		
		return(true);
		
	}//}}}//
}

