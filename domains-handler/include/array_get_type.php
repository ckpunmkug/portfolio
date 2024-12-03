<?php 

/// Getting typed variables from an array

function array_get_bool(string $key, array $array)
{//{{{
	if(!array_key_exists($key, $array)) {
		trigger_error("Given key not exists in array", E_USER_WARNING);
		return(false);
	}
	
	$result = boolval($array[$key]);
	return($result);
}//}}}

function array_get_int(string $key, array $array)
{//{{{
	if(!array_key_exists($key, $array)) {
		trigger_error("Given key not exists in array", E_USER_WARNING);
		return(false);
	}
	
	$result = intval($array[$key]);
	return($result);
}//}}}

function array_get_float(string $key, array $array)
{//{{{
	if(!array_key_exists($key, $array)) {
		trigger_error("Given key not exists in array", E_USER_WARNING);
		return(false);
	}
	
	$result = floatval($array[$key]);
	return($result);
}//}}}

function array_get_string(string $key, array $array)
{//{{{
	if(!array_key_exists($key, $array)) {
		trigger_error("Given key not exists in array", E_USER_WARNING);
		return(false);
	}
	
	$result = strval($array[$key]);
	return($result);
}//}}}

function array_get_array(string $key, array $array)
{//{{{
	if(!array_key_exists($key, $array)) {
		trigger_error("Given key not exists in array", E_USER_WARNING);
		return(false);
	}
	
	if(!is_array($array[$key])) {
		trigger_error("Value of array element with given key is not array", E_USER_WARNING);
		return(false);
	}
	
	return($array[$key]);
}//}}}

