<?php

class Data
{
	static $SHOW = [];
	static $SELECT = [];
	static $CREATE = [];
	static $DROP = [];
	static $DELETE = [];
	static $GRANT = [];
	static $REVOKE = [];
	static $ALTER = [];
}

/// Database ///////////////////////////////////////////////////////////////////

Data::$SHOW["DATABASE"] = function()
{//{{{//

	$sql = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
SHOW databases;
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		
	$ROW = Database::query($sql);
	if(!is_array($ROW)) {
		trigger_error("Can't 'SHOW DATABASE'", E_USER_WARNING);
		return(false);
	}
	
	$DATABASE = [];
	foreach($ROW as $row) {
		if(in_array($row["Database"], FILTER["database"])) continue;
		array_push($DATABASE, $row["Database"]);
	}
	
	return($DATABASE);
	
};//}}}//

Data::$CREATE["database"] = function(string $database)
{//{{{//
	
	$return = Database::check('database', $database);
	if(!$return) return(false);
	
	$sql = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
CREATE DATABASE IF NOT EXISTS {$database} CHARACTER SET utf8;
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		
	$return = Database::query($sql);
	if(!$return) {
		trigger_error("Can't 'CREATE database'", E_USER_WARNING);
		return(false);
	}
	
	return(true);
	
};//}}}//

Data::$DROP["database"] = function(string $database)
{//{{{//

	$return = Database::check('database', $database);
	if(!$return) return(false);
	
	$sql = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
DROP DATABASE IF EXISTS {$database};
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
	
	$return = Database::query($sql);
	if(!$return) return(false);
	
	return(true);
	
};//}}}//

/// User ///////////////////////////////////////////////////////////////////////

Data::$CREATE["user"] = function(string $user, string $password)
{//{{{//

	$return = Database::check('user', $user);
	if(!$return) return(false);

	$return = Database::check('password', $password);
	if(!$return) return(false);
	
	$sql = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
CREATE USER IF NOT EXISTS '{$user}'@'localhost' IDENTIFIED BY '{$password}';
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
	
	$return = Database::query($sql);
	if(!$return) {
		trigger_error("Can't 'CREATE user'", E_USER_WARNING);
		return(false);
	}
	
	return(true);
	
};//}}}//

Data::$SELECT["USER"] = function()
{//{{{//
	
	$sql = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
SELECT * FROM mysql.user;
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
	
	$ROW = Database::query($sql);
	if(!is_array($ROW)) {
		trigger_error("Can't 'SELECT USER'", E_USER_WARNING);
		return(false);
	}
	
	$USER = [];
	foreach($ROW as $row) {
		if(in_array($row["User"], FILTER["user"])) continue;
		array_push($USER, $row["User"]);
	}
	
	return($USER);
	
};//}}}//

Data::$DROP["user"] = function(string $user)
{//{{{//

	$return = Database::check('user', $user);
	if(!$return) return(false);
	
	$sql = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
DROP USER IF EXISTS '{$user}'@'localhost';
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
	
	$return = Database::query($sql);
	if(!$return) {
		trigger_error("Can't 'DROP user'", E_USER_WARNING);
		return(false);
	}
	
	return(true);
	
};//}}}//

Data::$GRANT["ALL"] = function(string $user, string $database)
{//{{{//
	
	$return = Database::check('user', $user);
	if(!$return) return(false);
	
	$return = Database::check('database', $database);
	if(!$return) return(false);
	
	$sql = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
GRANT ALL ON {$database}.* TO '{$user}'@'localhost';
HEREDOC;
///////////////////////////////////////////////////////////////}}}//

	$return = Database::query($sql);
	if(!$return) {
		trigger_error("Can't 'GRANT ALL'", E_USER_WARNING);
		return(false);	
	}
	
	return(true);
	
};//}}}//

Data::$REVOKE["ALL"] = function(string $user, string $database)
{//{{{//
	
	$return = Database::check('user', $user);
	if(!$return) return(false);
	
	$return = Database::check('database', $database);
	if(!$return) return(false);
	
	$sql = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
REVOKE ALL ON {$database}.* FROM '{$user}'@'localhost';
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
	
	$return = Database::query($sql);
	if(!$return) {
		trigger_error("Can't 'REVOKE ALL'", E_USER_WARNING);
		return(false);
	}
	
	return(true);
	
};//}}}//

Data::$SHOW["GRANTS"] = function(string $user)
{//{{{//
	
	$return = Database::check('user', $user);
	if(!$return) return(false);
	
	$sql = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
SHOW GRANTS FOR '{$user}'@'localhost';
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
	
	$ROW = Database::query($sql);
	if(!is_array($ROW)) {
		trigger_error("Can't 'SHOW GRANTS'", E_USER_WARNING);
		return(false);
	}
	
	$DATABASE = [];
	$pattern = '/^GRANT ALL PRIVILEGES ON `([_0-9a-zA-Z]+)`.+$/';
	foreach($ROW as $row) {
		$string = array_shift($row);
		$return = preg_match($pattern, $string, $MATCH);
		if($return != 1) continue;
		array_push($DATABASE, $MATCH[1]);
	}
	
	return($DATABASE);
	
};//}}}//

Data::$ALTER["password"] = function(string $user, string $password)
{//{{{//
	
	$return = Database::check('user', $user);
	if(!$return) return(false);
	
	$return = Database::check('password', $password);
	if(!$return) return(false);
	
	$sql = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
ALTER USER '{$user}'@'localhost' IDENTIFIED BY '{$password}';
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
	
	$return = Database::query($sql);
	if(!$return) {
		trigger_error("Can't 'ALTER password'", E_USER_WARNING);
		return(false);
	}
	
	return(true);
	
};//}}}//

/// Table //////////////////////////////////////////////////////////////////////

Data::$SHOW["TABLE"] = function(string $database)
{//{{{//
	
	$return = Database::check('database', $database);
	if(!$return) return(false);
	
	$sql = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
SHOW TABLES FROM {$database};
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
	
	$ROW = Database::query($sql);
	if(!is_array($ROW)) {
		trigger_error("Can't 'SHOW TABLE'", E_USER_WARNING);
		return(false);
	}
	
	$TABLE = [];
	foreach($ROW as $row) {
		$table = array_shift($row);
		array_push($TABLE, $table);
	}
	
	return($TABLE);
	
};//}}}//

Data::$DROP["TABLE"] = function(string $database, array $TABLE)
{//{{{//
	
	$return = Database::check('database', $database);
	if(!$return) return(false);
	
	foreach($TABLE as $table) {
	
		$return = Database::check('table', $table);
		if(!$return) return(false);
	
		$sql = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
DROP TABLE IF EXISTS {$database}.`{$table}`;
HEREDOC;
///////////////////////////////////////////////////////////////}}}//

		$return = Database::query($sql);
		if(!$return) {
			trigger_error("Can't 'DROP TABLE'", E_USER_WARNING);
			return(false);
		}

	}// foreach($TABLE as $table)
	
	return(true);
	
};//}}}//

