<?php

require_once('function/random_string.php');

function main(array $argv)
{	
	ArgV::$description = "Databases and users simple administration";
	ArgV::apply();

	$return = Database::open('root');
	if(!$return) {
		trigger_error("Can't open database", E_USER_WARNING);
		return(false);
	}
	
	label_0:
	if(true) // Main menu
	{//{{{//
		
		$return = Dialog::menu([
			"a" => 'Lists the databases',
			"b" => 'Lists the users',
			"c" => 'Lists the tables',
			"1" => 'CREATE DATABASE',
			"2" => 'CREATE USER',
			"3" => 'GRANT ALL ON',
			"4" => 'REVOKE ALL ON',
			"5" => 'DROP USER',
			"6" => 'DROP DATABASE',
			"7" => 'DELETE ALL TABLES',
			"8" => 'ALTER USER',
			"9" => 'CHEAT SHEETS',
		]);
		
		if($return["status"] == 1) {
			return(true);
		}
		
		switch($return["stderr"]) {
			case('a'):
			goto label_a0;
			break;
			
			case('b'):
			goto label_b0;
			break;
			
			case('c'):
			goto label_c0;
			break;
			
			case('1'):
			goto label_10;
			break;
			
			case('2'):
			goto label_20;
			break;
			
			case('3'):
			goto label_30;
			break;
			
			case('4'):
			goto label_40;
			break;
			
			case('5'):
			goto label_50;
			break;
			
			case('6'):
			goto label_60;
			break;
			
			case('7'):
			goto label_70;
			break;
			
			case('8'):
			goto label_80;
			break;
			
			case('9'):
			goto label_90;
			break;
		}
		
		return(true);
		
	}//}}}//
	
	label_a0:
	if(true) // Lists the databases
	{//{{{//
		
		$DATABASE = Data::$SHOW["DATABASE"]();
		if(!is_array($DATABASE)) return(false);
		
		if(count($DATABASE) == 0) {
			Dialog::msgbox("Databases not exists");
			goto label_0;
		}
		
		$text = implode("\n", $DATABASE);
		Dialog::msgbox($text);
		
		goto label_0;
		
	}//}}}//
	
	label_b0:
	if(true) // Lists the users 
	{//{{{//
		
		$USER = Data::$SELECT["USER"]();
		if(!is_array($USER)) return(false);
		
		if(count($USER) == 0) {
			Dialog::msgbox("Users not exists");
			goto label_0;
		}
		
		$text = implode("\n", $USER);
		Dialog::msgbox($text);
		
		goto label_0;
		
	}//}}}//
	
	label_c0:
	if(true) // Lists the tables
	{//{{{//
		
		$DATABASE = Data::$SHOW["DATABASE"]();
		if(!is_array($DATABASE)) return(false);
		
		if(count($DATABASE) == 0) {
			Dialog::msgbox("Databases not exists");
			goto label_0;
		}
		
		$return = Dialog::menu($DATABASE, "Databases");
		if($return["status"] == 1) goto label_0;
		
		$index = intval($return["stderr"]);
		$database = $DATABASE[$index];
		
		$TABLE = Data::$SHOW["TABLE"]($database);
		if(!is_array($TABLE)) return(false);
		
		$text = implode("\n", $TABLE);
		Dialog::msgbox($text);
		
		goto label_0;
		
	}//}}}//
	
	label_10:
	if(true) // CREATE DATABASE
	{//{{{//
		
		$return = Dialog::inputbox("database [_0-9a-zA-Z]+");
		if($return["status"] == 1) goto label_0;
		
		$database = $return["stderr"];
		
		$return = Database::check('database', $database);
		if(!$return) {
			Dialog::msgbox("Incorrect database");
			goto label_10;
		}
		
		$return = Data::$CREATE["database"]($database);
		if(!$return) return(false);
		
		goto label_0;
		
	}//}}}//
	
	label_20:
	if(true) // CREATE USER
	{//{{{//
		
		$return = Dialog::inputbox("user [\-_0-9a-zA-Z]+");	
		if($return["status"] == 1) goto label_0;
		
		$user = $return["stderr"];
		
		$return = Database::check('user', $user);
		if(!$return) {
			Dialog::msgbox("Incorrect user");
			goto label_20;
		}
		
		label_020:
		
		$password = random_string(8);
		$return = Dialog::inputbox('password [^\x00-\x1F\'\\\]+', $password);
		if($return["status"] == 1) goto label_0;
		
		$password = $return["stderr"];
		
		$return = Database::check('password', $password);
		if(!$return) {
			Dialog::msgbox("Incorrect password");
			goto label_020;
		}
		
		$return = Data::$CREATE["user"]($user, $password);
		if(!$return) return(false);
		
		goto label_0;
		
	}//}}}//
	
	label_30:
	if(true) // GRANT ALL ON
	{//{{{//
		
		$USER = Data::$SELECT["USER"]();
		if(!is_array($USER)) return(false);
		
		if(count($USER) == 0) {
			Dialog::msgbox("Users not exists");
			goto label_0;
		}
		
		$return = Dialog::menu($USER, "Users");
		if($return["status"] == 1) goto label_0;
		
		$index = intval($return["stderr"]);
		$user = $USER[$index];
		
		$DATABASE = Data::$SHOW["DATABASE"]();
		if(!is_array($DATABASE)) return(false);
		
		if(count($DATABASE) == 0) {
			Dialog::msgbox("Databases not exists");
			goto label_0;
		}
		
		$return = Dialog::menu($DATABASE, "Databases");
		if($return["status"] == 1) goto label_0;
		
		$index = intval($return["stderr"]);
		$database = $DATABASE[$index];
		
		$return = Data::$GRANT["ALL"]($user, $database);
		if(!$return) return(false);
		
		goto label_0;
		
	}//}}}//
	
	label_40:
	if(true) // REVOKE ALL
	{//{{{//
		
		$USER = Data::$SELECT["USER"]();
		if(!is_array($USER)) return(false);
		
		if(count($USER) == 0) {
			Dialog::msgbox("Users not exists");
			goto label_0;
		}
		
		$return = Dialog::menu($USER, "Users");
		if($return["status"] == 1) goto label_0;
		
		$index = intval($return["stderr"]);
		$user = $USER[$index];
		
		$DATABASE = Data::$SHOW["GRANTS"]($user);
		if(!is_array($DATABASE)) return(false);
		
		if(count($DATABASE) == 0) {
			Dialog::msgbox("Databases not granted");
			goto label_0;
		}
		
		$return = Dialog::menu($DATABASE, "Databases");
		if($return["status"] == 1) goto label_0;
		
		$index = intval($return["stderr"]);
		$database = $DATABASE[$index];
		
		$return = Dialog::yesno("REVOKE ALL ON {$database} FOR {$user}");
		if($return["status"] == 1) goto label_0;
		
		$return = Data::$REVOKE["ALL"]($user, $database);
		if(!$return) return(false);
		
		goto label_0;
		
	}//}}}//
	
	label_50:
	if(true) // DROP USER
	{//{{{//
		
		$USER = Data::$SELECT["USER"]();
		if(!is_array($USER)) return(false);
		
		if(count($USER) == 0) {
			Dialog::msgbox("Users not exists");
			goto label_0;
		}
		
		$return = Dialog::menu($USER, "Users");
		if($return["status"] == 1) goto label_0;
		
		$index = intval($return["stderr"]);
		$user = $USER[$index];
		
		$return = Dialog::yesno("DROP USER {$user}");
		if($return["status"] == 1) goto label_0;
		
		$return = Data::$DROP["user"]($user);
		if(!$return) return(false);
		
		goto label_0;
		
	}//}}}//
	
	label_60:
	if(true) // DROP DATABASE
	{//{{{//
	
		$DATABASE = Data::$SHOW["DATABASE"]();
		if(!is_array($DATABASE)) return(false);
		
		if(count($DATABASE) == 0) {
			Dialog::msgbox("Databases not exists");
			goto label_0;
		}
		
		$return = Dialog::menu($DATABASE, "Databases");
		if($return["status"] == 1) goto label_0;
		
		$index = intval($return["stderr"]);
		$database = $DATABASE[$index];
		
		$return = Dialog::yesno("DROP DATABASE {$database}");
		if($return["status"] == 1) goto label_0;
		
		$return = Data::$DROP["database"]($database);
		if(!$return) return(false);
		
		goto label_0;
		
	}//}}}//
	
	label_70:
	if(true) // DELETE ALL TABLES
	{//{{{//
		
		$DATABASE = Data::$SHOW["DATABASE"]();
		if(!is_array($DATABASE)) return(false);
		
		if(count($DATABASE) == 0) {
			Dialog::msgbox("Databases not exists");
			goto label_0;
		}
		
		$return = Dialog::menu($DATABASE, "Databases");
		if($return["status"] == 1) goto label_0;
		
		$index = intval($return["stderr"]);
		$database = $DATABASE[$index];
		
		$return = Dialog::yesno("DELETE ALL TABLES IN {$database}");
		if($return["status"] == 1) goto label_0;
		
		$TABLE = Data::$SHOW["TABLE"]($database);
		if(!is_array($TABLE)) return(false);
		
		$return = Data::$DROP["TABLE"]($database, $TABLE);
		if(!$return) return(false);
		
		goto label_0;
		
	}//}}}//
	
	label_80:
	if(true) // ALTER USER
	{//{{{//
		
		$USER = Data::$SELECT["USER"]();
		if(!is_array($USER)) return(false);
		
		if(count($USER) == 0) {
			Dialog::msgbox('Users not exists');
			goto label_0;
		}
		
		$return = Dialog::menu($USER, 'Users');
		if($return["status"] == 1) goto label_0;
		
		$index = intval($return["stderr"]);
		$user = $USER[$index];
		
		$password = random_string(8);
		$return = Dialog::inputbox('Password', $password);
		if($return["status"] == 1) goto label_0;
		
		$password = $return["stderr"];
		$return = Data::$ALTER["password"]($user, $password);
		if(!$return) return(false);
		
		goto label_0;
		
	}//}}}//
	
	label_90:
	if(true) // CHEAT SHEETS
	{//{{{//
		
		$file = __DIR__.'/cheat_sheets.txt';
		Dialog::textbox($file);
	
		goto label_0;
		
	}//}}}//
	
	return(true);	
}

