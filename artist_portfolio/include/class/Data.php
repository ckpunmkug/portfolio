<?php

class Data
{
	static function create_tables()
	{//{{{
		$DB = new DB;
		$sql = 
<<<HEREDOC
DROP TABLE IF EXISTS `projects`;
CREATE TABLE `projects` (
	`id` INT AUTO_INCREMENT KEY,
	`title` TEXT,
	`image` TEXT,
	`text1` TEXT,
	`text2` TEXT
);

DROP TABLE IF EXISTS `about`;
CREATE TABLE `about` (
	`title` TEXT,
	`image` TEXT,
	`text1` TEXT,
	`text2` TEXT
);

DROP TABLE IF EXISTS `contacts`;
CREATE TABLE `contacts` (
	`vk` TEXT,
	`telegram` TEXT,
	`email` TEXT
);
HEREDOC;
		$return = $DB->queries($sql);
		if(!$return) {
			trigger_error("Can't perform database query", E_USER_WARNING);
			return(false);
		}
		return(true);
	}//}}}

	static function select_project(int $id)
	{//{{{
		$DB = new DB;
		$sql = 
<<<HEREDOC
SELECT `id`, `title`, `image`, `text1`, `text2` 
FROM `projects` WHERE `id`=$id LIMIT 1;

HEREDOC;
		$result = $DB->query($sql);
		if(!is_array($result)) {
			trigger_error("Can't perform database query", E_USER_WARNING);
			return(false);
		}
		
		if(empty($result)) {
			return(NULL);
		}
		
		return($result[0]);
	}//}}}

	static function select_projects()
	{//{{{
		$DB = new DB;
		$sql = 
<<<HEREDOC
SELECT `id`, `title`, `image`, `text1`, `text2` FROM `projects`;
HEREDOC;
		$result = $DB->query($sql);
		if(!is_array($result)) {
			trigger_error("Can't perform database query", E_USER_WARNING);
			return(false);
		}
		
		return($result);
	}//}}}

	static function new_project(string $title, string $image, string $text1, string $text2)
	{//{{{
		$DB = new DB;
		$_ = [];
		$_["title"] = $DB->escape($title);
		$_["image"] = $DB->escape($image);
		$_["text1"] = $DB->escape($text1);
		$_["text2"] = $DB->escape($text2);
		$sql = 
<<<HEREDOC
INSERT INTO `projects` (
	`title`, `image`, `text1`, `text2`
) VALUES (
	'{$_["title"]}', '{$_["image"]}', '{$_["text1"]}', '{$_["text2"]}'
);
HEREDOC;
		$return = $DB->queries($sql);
		if(!$return) {
			trigger_error("Can't perform database query", E_USER_WARNING);
			return(false);
		}
		$id = $DB->id();
		return($id);
	}//}}}

	static function save_project(int $id, string $title, string $image, string $text1, string $text2)
	{//{{{
		$DB = new DB;
		$_ = [];
		$_["title"] = $DB->escape($title);
		$_["image"] = $DB->escape($image);
		$_["text1"] = $DB->escape($text1);
		$_["text2"] = $DB->escape($text2);
		$sql = 
<<<HEREDOC
UPDATE `projects` SET
	`title` = '{$_["title"]}',
	`image` = '{$_["image"]}', 
	`text1` = '{$_["text1"]}',
	`text2` = '{$_["text2"]}'
WHERE `id`={$id};
HEREDOC;
		$return = $DB->queries($sql);
		if(!$return) {
			trigger_error("Can't perform database query", E_USER_WARNING);
			return(false);
		}
		return($id);
		
	}//}}}
	
	static function delete_project(int $id)
	{//{{{
		$DB = new DB;
		$sql = 
<<<HEREDOC
DELETE FROM `projects` WHERE `id`={$id};
HEREDOC;
		$result = $DB->query($sql);
		if(!$result) {
			trigger_error("Can't perform database query", E_USER_WARNING);
			return(false);
		}
		return(0);
	}//}}}

	static function put_about(string $title, string $image, string $text1, string $text2)
	{//{{{
		$path = Main::SCRIPT_DIR.'/'.Main::UPLOAD_DIR;
		
		$DB = new DB;
		
		$sql = 
<<<HEREDOC
SELECT `image` FROM `about`;
HEREDOC;
		$result = $DB->query($sql);
		if(!is_array($result)) {
			trigger_error("Can't perform database query", E_USER_WARNING);
			return(false);
		}
		
		foreach($result as $array) {
			@unlink("{$path}/{$array['image']}");
		}
		
		$_ = [];
		$_["title"] = $DB->escape($title);
		$_["image"] = $DB->escape($image);
		$_["text1"] = $DB->escape($text1);
		$_["text2"] = $DB->escape($text2);
		$sql = 
<<<HEREDOC
DELETE FROM `about`;
INSERT INTO `about` (
	`title`, `image`, `text1`, `text2`
) VALUES (
	'{$_["title"]}', '{$_["image"]}', '{$_["text1"]}', '{$_["text2"]}'
);
HEREDOC;
		$return = $DB->queries($sql);
		if(!$return) {
			trigger_error("Can't perform database query", E_USER_WARNING);
			return(false);
		}
		return(true);
	}//}}}

	static function get_about()
	{//{{{
		$DB = new DB;
		$sql = 
<<<HEREDOC
SELECT `title`, `image`, `text1`, `text2` FROM `about` LIMIT 1;
HEREDOC;
		$result = $DB->query($sql);
		if(!is_array($result)) {
			trigger_error("Can't perform database query", E_USER_WARNING);
			return(false);
		}
		
		if(empty($result)) {
			return([
				"title" => '',
				"image" => '',
				"text1" => '',
				"text2" => '',
			]);
		}
		
		return($result[0]);
	}//}}}

	static function put_contacts(string $vk, string $telegram, string $email)
	{//{{{
		$DB = new DB;
		$_ = [];
		$_["vk"] = $DB->escape($vk);
		$_["telegram"] = $DB->escape($telegram);
		$_["email"] = $DB->escape($email);
		$sql = 
<<<HEREDOC
DELETE FROM `contacts`;
INSERT INTO `contacts` 
	(`vk`, `telegram`, `email`) 
VALUES 
	('{$_["vk"]}', '{$_["telegram"]}', '{$_["email"]}');
HEREDOC;
		$return = $DB->queries($sql);
		if(!$return) {
			trigger_error("Can't perform database query", E_USER_WARNING);
			return(false);
		}
		return(true);
	}//}}}

	static function get_contacts()
	{//{{{
		$DB = new DB;
		$sql = 
<<<HEREDOC
SELECT `vk`,`telegram`,`email` FROM `contacts` LIMIT 1;
HEREDOC;
		$array = $DB->query($sql);
		if(!is_array($array)) {
			trigger_error("Can't perform database query", E_USER_WARNING);
			return(false);
		}
		
		if(!(
			@is_string($array[0]["vk"])
			&& @is_string($array[0]["telegram"])
			&& @is_string($array[0]["email"])
		)) {
			return([
				"vk" => '',
				"telegram" => '',
				"email" => '',
			]);
		}
		
		return($array[0]);
	}//}}}

	static function get_project(int $id)
	{//{{{
		return(false);
	}//}}}

}


