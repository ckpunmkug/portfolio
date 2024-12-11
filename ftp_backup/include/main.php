<?php

function main(string $data_dir)
{
	if(true) // get exclude dates
	{//{{{//
		$orders = FTPBackup::get_orders(CONFIG["woocommerce"]);
		if(!is_array($orders)) {
			trigger_error("Can't get orders", E_USER_WARNING);
			return(false);
		}
		
		if(!(defined('QUIET') && QUIET)) {
			$count = count($orders);
			echo("Всего ордеров: {$count}\n");
		}
		
		$orders = FTPBackup::filter_orders($orders);
		
		if(!(defined('QUIET') && QUIET)) {
			$count = count($orders);
			$string = implode(', ', FTPBackup::$use_statuses);
			echo("Oрдеров типа - {$string}: {$count}\n");
		}
		
		$dates = FTPBackup::get_orders_dates($orders);
		
		$timestamp = time();
		for($i = 7; $i >= 0; $i--) {
			$n = $timestamp - $i*ONE_DAY;
			$date = date('d.m.y', $n);
			array_push($dates, $date);
		}
		
		if(!(defined('QUIET') && QUIET)) {
			$string = implode(', ', $dates);
			echo("Даты исключёные из списка копирования: {$string}\n");
		}
		
		$string = serialize($dates);
		$return = file_put_contents("{$data_dir}/dates.array", $string);
	}//}}}//
	
	$string = file_get_contents("{$data_dir}/dates.array");
	$dates = unserialize($string);
	
	$FTPBackup = new FTPBackup(CONFIG["source_ftp"], CONFIG["destination_ftp"]);
	
	if(true) // create files list
	{//{{{//
		$files = $FTPBackup->get_paths($FTPBackup->ftp["source"]);
		if(!is_array($files)) {
			trigger_error("Can't files paths from source ftp", E_USER_WARNING);
			return(false);
		}

		$files = $FTPBackup->get_timestamps($FTPBackup->ftp["source"], $files);
		if(!is_array($files)) {
			trigger_error("Can't files times from source ftp", E_USER_WARNING);
			return(false);
		}
		
		if(!(defined('QUIET') && QUIET)) {
			$count = count($files);
			echo("Всего файлов на исходном сервере: {$count}\n");
		}
		
		$files = FTPBackup::exclude_files($files, $dates);
		
		if(!(defined('QUIET') && QUIET)) {
				$count = count($files);
				echo("Файлов после исключения: {$count}\n");
		}
		
		$string = json_encode($files, JSON_PRETTY_PRINT);
		$return = file_put_contents("{$data_dir}/files.array", $string);
	}//}}}//
	
	$string = file_get_contents("{$data_dir}/files.array");
	$files = json_decode($string, true);

	if(true) // move files
	{//{{{//
		$target_dir = CONFIG["destination_ftp"]["directory"];
		$cd = count($files);
		foreach($files as $file) {
			$cd -= 1;
			if($cd < 0) break;
			
			if(defined('VERBOSE') && VERBOSE) {
					echo("\r Left: {$cd}      \r");
			}

			unset($FTPBackup);
			$FTPBackup = new FTPBackup(CONFIG["source_ftp"], CONFIG["destination_ftp"]);

			$return = $FTPBackup->move_file($file, $target_dir);
			if(!$return) {
				if (defined('DEBUG') && DEBUG) var_dump(['$file' => $file]);
				trigger_error("Can't move file", E_USER_WARNING);
				return(false);
			}
		}
	}//}}}//
	
}

