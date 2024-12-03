<?php 

/// Checking the configuration and connecting to the database

if(!true) // CONFIG
{//{{{
	define('CONFIG', [
		"database" => [
			"host" => 'localhost'
			,"user" => 'user'
			,"password" => 'password'
			,"database" => 'database'
		]
	]);
}//}}}

if(true) // DB::open
{//{{{
	if(!defined('CONFIG')) {
		trigger_error("Constant 'CONFIG' is not defined", E_USER_ERROR);
		exit(255);
	}

	if(!(
		@is_string(CONFIG["database"]["host"])
		&& @is_string(CONFIG["database"]["user"])
		&& @is_string(CONFIG["database"]["password"])
		&& @is_string(CONFIG["database"]["database"])
	)) {
		if(defined('DEBUG') && DEBUG) @var_dump(['CONFIG["database"]' => CONFIG["database"]]);
		trigger_error('Incorrect CONFIG["database"]', E_USER_ERROR);
		exit(255);
	}
	
	$mysqli = DB::open(
		CONFIG["database"]["host"]
		, CONFIG["database"]["user"]
		, CONFIG["database"]["password"]
		, CONFIG["database"]["database"]
	);
	if(!is_object($mysqli)) {
		if(defined('DEBUG') && DEBUG) var_dump(['CONFIG["database"]' => CONFIG["database"]]);
		trigger_error("Can't open connection to database", E_USER_ERROR);
		exit(255);
	}
}//}}}

