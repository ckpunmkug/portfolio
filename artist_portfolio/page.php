<?php

require_once(__DIR__.'/config.php');
require_once(__DIR__.'/include/class/DB.php');
require_once(__DIR__.'/include/class/Data.php');

$return = DB::open(
	CONFIG["database"]["host"],
	CONFIG["database"]["user"],
	CONFIG["database"]["password"],
	CONFIG["database"]["database"]
);
if(!is_object($return)) {
	trigger_error("Can't open database connection", E_USER_ERROR);
	exit(255);
}

if(isset($_GET['name'])) {
	switch($_GET['name']) {
		case('projects'):
			require_once(__DIR__.'/include/pages/projects.php');
			break;
		case('about'):
			require_once(__DIR__.'/include/pages/about.php');
			break;
		case('contacts'):
			require_once(__DIR__.'/include/pages/contacts.php');
			break;
		case('project'):
			require_once(__DIR__.'/include/pages/project.php');
			break;
	}
}

