<?php

define('ONE_DAY', 86400);
function date_to_timestamp(string $date)
{//{{{//
	$expression = '/^(\d+).(\d+).(\d+)$/';
	if(preg_match($expression, $date, $MATCH) != 1) {
		if (defined('DEBUG') && DEBUG) var_dump(['$date' => $date]);
		trigger_error("Can't parse 'date' string", E_USER_WARNING);
		return(false);
	}
	
	$day = intval($MATCH[1]);
	$month = intval($MATCH[2]);
	$year = intval($MATCH[3]);
	
	if($day < 1 || $day > 31) {
		if (defined('DEBUG') && DEBUG) var_dump(['$day' => $day]);
		trigger_error("The 'day' value is out of range", E_USER_WARNING);
		return(false);
	}
	
	if($month < 1 || $month > 12) {
		if (defined('DEBUG') && DEBUG) var_dump(['$month' => $month]);
		trigger_error("The 'month' value is out of range", E_USER_WARNING);
		return(false);
	}
	
	if($year > 69) {
		if (defined('DEBUG') && DEBUG) var_dump(['$year' => $year]);
		trigger_error("The 'year' value is out of range", E_USER_WARNING);
		return(false);
	}
	
	$string = sprintf("%02d-%02d-%02d", $year, $month, $day);
	$timestamp = strtotime($string);
	
	return($timestamp);
}//}}}//

