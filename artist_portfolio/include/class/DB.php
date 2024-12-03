<?php
class DB /// mysqli wrapper with extended tools functions.
{//{{{
	static $mysqli = NULL;
	
	static function // Opens a connection to the MariaDB server.
	open($host, $user, $password, $database) // false, mysqli_object
	{//{{{
		if(!is_string($host))
			trigger_error('Passed variable "$host" is not string', E_USER_ERROR);
			
		if(!is_string($user))
			trigger_error('Passed variable "$user" is not string', E_USER_ERROR);
			
		if(!is_string($password))
			trigger_error('Passed variable "$password" is not string', E_USER_ERROR);
		
		if(!is_string($database))
			trigger_error('Passed variable "$database" is not string', E_USER_ERROR);
	
		$mysqli = &DB::$mysqli;

		try {
			$mysqli = @new mysqli($host, $user, $password, $database);
			$errno = mysqli_connect_errno();
			if($errno !== 0) {
				trigger_error("Can't connect to database because: {$mysqli->connect_error}", E_USER_WARNING);
				return(false);
			}
		} catch(Exception $Exception) {
			if(defined('DEBUG') && DEBUG) var_dump([
				'$host' => $host
				,'$user' => $user
				,'$password' => $password
				,'$database' => $database
			]);
			$message = $Exception->getMessage();
			trigger_error("Can't connect to database because: {$message}", E_USER_WARNING);
			return(false);
		}

		try {
			$return = $mysqli->set_charset("utf8");
			if(!$return) {
				trigger_error("Can't set database client character", E_USER_WARNING);
				return(false);
			}
		} catch(Exception $Exception) {
			$message = $Exception->getMessage();
			trigger_error("Can't set database client character because: {$message}", E_USER_WARNING);
			return(false);
		}
		
		if(!defined('MYSQLI_OPT_INT_AND_FLOAT_NATIVE')) {
			define('MYSQLI_OPT_INT_AND_FLOAT_NATIVE', 201);
		}
		$mysqli->options(MYSQLI_OPT_INT_AND_FLOAT_NATIVE, TRUE);

		register_shutdown_function(function () {
			if(is_object(DB::$mysqli) && isset(DB::$mysqli->server_info)) {
				$return = DB::$mysqli->close();
				if(!$return) {
					trigger_error("Can't close database connection", E_USER_WARNING);
					return(false);
				}
				return(true);
			}
			return(NULL);
		} );
		
		return($mysqli);
	}//}}}
	
	function // Creates a class for working with an opened connection.
	__construct()
	{//{{{
		$mysqli = &DB::$mysqli;
		if (!is_object($mysqli)) {
			throw new Exception("Connection to database is not open");
		}
		return(NULL);
	}//}}}
	
	function // A wrapper for $mysqli->multi_query, used for sql queries without a result.
	queries($sql) // false, true
	{//{{{
		if(!is_string($sql))
			trigger_error('Passed variable "$sql" is not string', E_USER_ERROR);
		
		$mysqli = &DB::$mysqli;
		
		$return = $mysqli->multi_query($sql);
		if($return === false) {
			trigger_error($mysqli->error, E_USER_WARNING);
			return(false);
		}
		
		while($mysqli->more_results()) {
			$this->result = $mysqli->next_result();
			if ($this->result === false) {
				trigger_error($mysqli->error, E_USER_WARNING);
				return(false);
			}
		}
		
		return(true);
	}//}}}
	
	function // A wrapper for $mysqli->query used for a single sql query.
	query($sql) // false, true, assoc_array
	{//{{{
		if(!is_string($sql))
			trigger_error('Passed variable "$sql" is not string', E_USER_ERROR);
	
		$mysqli = &DB::$mysqli;
		
		$mysqli_result = $mysqli->query($sql);
		if ($mysqli_result === false) {
			trigger_error($mysqli->error, E_USER_WARNING);
			return(false);
		}
		
		if($mysqli_result === true) {
			return(true);
		}
		
		$result = [];
		while(true) {
			$row = $mysqli_result->fetch_assoc();
			if($row === NULL) break;
			array_push($result, $row);
		}
		
		return($result);
	}//}}}
	
	function // Returns the auto generated id used in the latest query
	id() // int
	{//{{{
		$return = DB::$mysqli->insert_id;
		return $return;
	}//}}}
	
	function escape(string $variable)
	{//{{{
		$return = DB::$mysqli->real_escape_string($variable);
		return $return;
	}//}}}

	function int($variable)
	{//{{{
		$number = intval($variable, 10);
		$number = strval($number);
		return $number;
	}//}}}

}

