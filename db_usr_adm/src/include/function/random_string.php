<?php
function random_string(int $random_string_length = 32)
{
	$chars = '0123456789abcdefjhijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$last_char_position = strlen($chars)-1;
	$random_string = '';
	
	for ($i = 0; $i < $random_string_length; $i++) {
		$position = rand(0, $last_char_position);
		$random_string .= substr($chars, $position, 1);
	}
	
	return $random_string;
}

