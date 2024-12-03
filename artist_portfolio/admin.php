<?php

define('DEBUG', true);

require_once(__DIR__.'/config.php');
require_once(__DIR__.'/include/class/DB.php');
require_once(__DIR__.'/include/class/HTML.php');
require_once(__DIR__.'/include/class/Data.php');

class Main
{
	const SCRIPT_DIR = __DIR__;
	const UPLOAD_DIR = 'assets/img/uploaded';
	
	var $style = 
//{{{
<<<HEREDOC
td {
	vertical-align: top;
}
img {
	max-width: 200px;
	max-height: 200px;
}
.projects {
	width: 200px;
	height: 20px;
	margin: 2px;
}
HEREDOC;
//}}}
	var $script = 
//{{{
<<<HEREDOC
var containers = {};
var sender = 'Super';

window.addEventListener("load", function() {
	/*
	sender = document.querySelector("iframe[name='sender']");
	sender.addEventListener("load", function(){
		let div = sender.document.getElementById("0");
		console.log(div);
	});
	
	containers.project = document.querySelector("div[name='project']");
	*/
	
});

HEREDOC;
//}}}
	
	var $update_projects =
//{{{
<<<HEREDOC
window.addEventListener("load", function(event) {
	let source_div = document.querySelector("div[name='projects_inputs']");
	let dest_div = window.parent.document.querySelector("div[name='projects_inputs']");
	dest_div.innerHTML = source_div.innerHTML;
});
HEREDOC;
//}}}
	
	var $update_project =
//{{{
<<<HEREDOC
window.addEventListener("load", function(event) {
	let source_div = document.querySelector("div[name='project_inputs']");
	let dest_div = window.parent.document.querySelector("div[name='project_inputs']");
	dest_div.innerHTML = source_div.innerHTML;
});
HEREDOC;
//}}}
	
	var $update_about =
//{{{
<<<HEREDOC
window.addEventListener("load", function(event) {
	let source_div = document.querySelector("div[name='about_inputs']");
	let dest_div = window.parent.document.querySelector("div[name='about_inputs']");
	dest_div.innerHTML = source_div.innerHTML;
});
HEREDOC;
//}}}
	
	var $update_contacts =
//{{{
<<<HEREDOC
window.addEventListener("load", function(event) {
	let source_div = document.querySelector("div[name='contacts_inputs']");
	let dest_div = window.parent.document.querySelector("div[name='contacts_inputs']");
	dest_div.innerHTML = source_div.innerHTML;
});
HEREDOC;
//}}}
	
	function __construct()
	{//{{{
		$return = $this->connect_to_database();
		if(!$return) {
			trigger_error("Can't connect to database", E_USER_WARNING);
			return(false);
		}
		
		switch($_SERVER['REQUEST_METHOD']) {
			case('GET'):
				$return = $this->handle_get_request();
				if(!$return) {
					trigger_error("Handle get request failed", E_USER_ERROR);
					exit(255);
				}
				break;
			case('POST'):
				$return = $this->handle_post_request();
				if(!$return) {
					trigger_error("Handle post request failed", E_USER_ERROR);
					exit(255);
				}
				break;
		}
	}//}}}

	function __destruct()
	{//{{{
		$html = HTML::generate();
		echo($html);
	}//}}}

	function connect_to_database()
	{//{{{
		$return = DB::open(
			CONFIG["database"]["host"],
			CONFIG["database"]["user"],
			CONFIG["database"]["password"],
			CONFIG["database"]["database"]
		);
		if(!is_object($return)) {
			trigger_error("Can't open database connection", E_USER_WARNING);
			return(false);
		}
		return(true);
	}//}}}

	function handle_get_request()
	{//{{{
		$action = '';
		if(isset($_GET["action"]) && is_string($_GET["action"])) {
			$action = $_GET["action"];
		}
		
		switch($action) {
			case(''):
				$return = $this->containers();
				break;
				
			case('edit_project'):
				//{{{
				if(!( @is_string($_GET["id"]) )) {
					if(defined('DEBUG') && DEBUG) var_dump(['$_GET["id"]' => $_GET["id"]]);
					trigger_error("Incorrect 'id'", E_USER_WARNING);
					return(false);
				}
				$id = intval($_GET['id']);
				
				$project = $this->get_project($id);
				if(!is_array($project)) {
					trigger_error("Can't get project", E_USER_WARNING);
					return(false);
				}
				
				$project_inputs = $this->create_project_inputs($project);
				HTML::$body = 
<<<HEREDOC
<div name="project_inputs">
{$project_inputs}
</div>

HEREDOC;
				HTML::$script = $this->update_project;
				return(true);
				//}}}
			
			default:
				trigger_error("Unsupported 'action'", E_USER_WARNING);
				return(false);
		}
		
		return($return);
	}//}}}
	
	function handle_post_request()
	{//{{{
		$action = '';
		if(isset($_POST["action"]) && is_string($_POST["action"])) {
			$action = $_POST["action"];
		}
		
		switch($action) {
			case("create_tables"):
				//{{{
				$return = $this->create_tables();
				if(!$return) {
					HTML::$script .= 'alert("Can\'t create tables in database");';
					trigger_error("Can't create tables in database", E_USER_WARNING);
					return(false);
				}
				
				HTML::$script .= 'alert("Tables was created in database");';
				
				return(true);
				//}}}
		
			case("project_operation"):
				//{{{
				if(!@is_string($_POST["command"])) {
					trigger_error("Incorrect 'command'", E_USER_WARNING);
					return(false);
				}
				
				switch($_POST["command"]) {
					case("New"):
						$project_id = $this->new_project();
						break;
					case("Save"):
						$project_id = $this->save_project();
						break;
					case("Delete"):
						$project_id = $this->delete_project();
						break;	
					default:
						trigger_error("Unsupported 'command'", E_USER_WARNING);
						return(false);
				}
				if(!is_int($project_id)) {
					if(defined('DEBUG') && DEBUG) var_dump(['$_POST["command"]' => $_POST["command"]]);
					trigger_error("Command in project operation failed", E_USER_WARNING);
					return(false);
				}
				
				$projects = $this->get_projects();
				$projects_inputs = $this->create_projects_inputs($projects);
				$project_inputs = $this->create_project_inputs();
				
				HTML::$body = 
<<<HEREDOC
<div name="projects_inputs">{$projects_inputs}</div>
<div name="project_inputs">{$project_inputs}</div>

HEREDOC;
				HTML::$script .= $this->update_projects;
				HTML::$script .= $this->update_project;
				return(true);
				//}}}
			
			case("save_about"):
				//{{{
				$return = $this->save_about();
				if(!$return) {
					trigger_error("Can't save about", E_USER_WARNING);
					return(false);
				}
				
				$about = $this->get_about();
				$about_inputs = $this->create_about_inputs($about);
				
				$body = 
<<<HEREDOC
<div name="about_inputs">
{$about_inputs}
</div>
HEREDOC;
				HTML::$body = $body;
				HTML::$script .= $this->update_about;
				
				return(true);
				//}}}
			
			case("save_contacts"):
				//{{{
				$return = $this->save_contacts();
				if(!$return) {
					trigger_error("Can't save contacts", E_USER_WARNING);
					return(false);
				}
				
				$contacts = $this->get_contacts();
				$contacts_inputs = $this->create_contacts_inputs($contacts);
				
				$body = 
<<<HEREDOC
<div name="contacts_inputs">
{$contacts_inputs}
</div>
HEREDOC;
				HTML::$body = $body;
				HTML::$script .= $this->update_contacts;
				return(true);
				//}}}
			
			default:
				trigger_error("Unsupported 'action'", E_USER_WARNING);
				return(false);
		}
		return(false);
	}//}}}


	function containers()
	{//{{{
		HTML::$style .= $this->style;
	
		$projects = $this->get_projects();		
		$projects_inputs = $this->create_projects_inputs($projects);
		$project_inputs = $this->create_project_inputs();
		
		$about = $this->get_about();
		$about_inputs = $this->create_about_inputs($about);
		
		$contacts = $this->get_contacts();
		$contacts_inputs = $this->create_contacts_inputs($contacts);

		$body = 
<<<HEREDOC
<table>
<tr><td>
	<form name="project" action="admin.php" method="post" target="sender">
		<input name="action" value="create_tables" type="hidden" />
		<input name="command" value="Create tables in database" type="submit" />
	</form>
		
	<fieldset name="projects">
		<legend>Projects</legend>
	<div name="projects_inputs">{$projects_inputs}</div>
	</fieldset>
	
	<fieldset name="contacts">
		<legend>Contacts</legend>
		<form name="contacts" action="admin.php" method="post" target="sender">
			<input name="action" value="save_contacts" type="hidden" />
			<div name="contacts_inputs">{$contacts_inputs}</div>
			<br />
			<input name="command" value="Save" type="submit" />
		</form>
	</fieldset>
</td>
<td>
	<fieldset name="project">
		<legend>Project</legend>
		<form name="project" action="admin.php" method="post" target="sender" accept="png,jpg,jpeg" enctype="multipart/form-data">
			<div name="project_inputs">{$project_inputs}</div>
			<br />
			<input name="action" value="project_operation" type="hidden" />
			<input name="command" value="New" type="submit" />
			<input name="command" value="Save" type="submit" />
			<input name="command" value="Delete" type="submit" />
		</form>
	</fieldset>
</td>
<td>
	<fieldset name="about">
		<legend>About me</legend>
		<form name="project" action="admin.php" method="post" target="sender" accept="png,jpg,jpeg" enctype="multipart/form-data">
			<input name="action" value="save_about" type="hidden" />
			<div name="about_inputs">{$about_inputs}</div>
			<br />
			<input name="command" value="Save" type="submit" />
		</form>
	</fieldset>
</tr>
</table>

<iframe name="sender" width="0" height="0"></iframe>
HEREDOC;
		HTML::$body .= $body;
		return(true);
	}//}}}


	function create_projects_inputs(array $projects)
	{//{{{
		$inputs = '';
		foreach($projects as $key => $project) {
			if(!( @is_int($project["id"]) && @is_string($project["title"]) )) {
				if(defined('DEBUG') && DEBUG) var_dump(['$project' => $project]);
				trigger_error("Incorrect 'project'", E_USER_WARNING);
				return(false);
			}
			
			$project["title"] = htmlentities($project["title"]);
			$key = htmlentities($key);
			$href = "admin.php?action=edit_project&id={$project['id']}";
			$inputs .= 
<<<HEREDOC
<a href="{$href}" target="sender">
	<button class="projects">{$project["title"]}</button>
</a><br />

HEREDOC;
		}
		
		return($inputs);
	}//}}}

	function create_project_inputs(array $project = [])
	{//{{{
		$id = '';
		if(@is_int($project["id"]))
			$id = $project["id"];
		
		$title = '';
		if(@is_string($project["title"]))
			$title = htmlentities($project["title"]);
			
		$image = '';
		if(@is_string($project["image"]))
			$image = htmlentities($project["image"]);
			
		$text1 = '';
		if(@is_string($project["text1"]))
			$text1 = htmlentities($project["text1"]);
			
		$text2 = '';
		if(@is_string($project["text2"]))
			$text2 = htmlentities($project["text2"]);
		
		$upload_dir = self::UPLOAD_DIR;
		$inputs = 
<<<HEREDOC
<input name="id" value="{$id}" type="hidden" />
<label>
	Title<br />
	<input name="title" value="{$title}" type="text" /><br/>
</label>
<label>
	Image<br />
	<img src="{$upload_dir}/{$image}" /><br />
	<input name="image" type="file" /><br/>
</label>
<label>
	Text1<br />
	<input name="text1" type="text" value="{$text1}" /><br/>
</label>
<label>
	Text2<br />
	<input name="text2" type="text" value="{$text2}" /><br/>
</label>
HEREDOC;
		return($inputs);
	}//}}}

	function create_about_inputs(array $about = [])
	{//{{{
		$title = '';
		if(@is_string($about["title"]))
			$title = htmlentities($about["title"]);
			
		$image = '';
		if(@is_string($about["image"]))
			$image = htmlentities($about["image"]);
			
		$text1 = '';
		if(@is_string($about["text1"]))
			$text1 = htmlentities($about["text1"]);
			
		$text2 = '';
		if(@is_string($about["text2"]))
			$text2 = htmlentities($about["text2"]);
		
		$upload_dir = self::UPLOAD_DIR;
		$inputs = 
<<<HEREDOC
<label>
	Title<br />
	<input name="title" value="{$title}" type="text" /><br/>
</label>
<label>
	Image<br />
	<img src="{$upload_dir}/{$image}" /><br />
	<input name="image" type="file" /><br/>
</label>
<label>
	Text1<br />
	<input name="text1" type="text" value="{$text1}" /><br/>
</label>
<label>
	Text2<br />
	<input name="text2" type="text" value="{$text2}" /><br/>
</label>
HEREDOC;
		return($inputs);
	}//}}}

	function create_contacts_inputs(array $contacts = [])
	{//{{{
		$vk = '';
		if(@is_string($contacts["vk"]))
			$vk = htmlentities($contacts["vk"]);
			
		$telegram = '';
		if(@is_string($contacts["telegram"]))
			$telegram = htmlentities($contacts["telegram"]);
			
		$email = '';
		if(@is_string($contacts["email"]))
			$email = htmlentities($contacts["email"]);
			
		$inputs = 
<<<HEREDOC
<label>
	VK<br />
	<input name="vk" value="{$vk}" type="text" /><br/>
</label>
<label>
	Telegram<br />
	<input name="telegram" value="{$telegram}" type="text" /><br/>
</label>
<label>
	Email<br />
	<input name="email" value="{$email}" type="text" /><br/>
</label>
HEREDOC;
		return($inputs);
	}//}}}

	
	function new_project()
	{//{{{
		if(!(
			@is_string($_POST["title"])
			&& @is_string($_POST["text1"])
			&& @is_string($_POST["text2"])
		)) {
			if(defined('DEBUG') && DEBUG) var_dump(['$_POST' => $_POST]);
			trigger_error("Incorrect incoming data", E_USER_WARNING);
			return(false);
		}
		
		$basename = '';
		if(@is_array($_FILES["image"]) && @$_FILES["image"]["error"] === 0) {
		
			$extension = pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION);
			$filename = pathinfo($_FILES["image"]["tmp_name"], PATHINFO_BASENAME);
			
			$basename = "{$filename}.{$extension}";
			$return = rename($_FILES["image"]["tmp_name"], __DIR__."/".self::UPLOAD_DIR."/{$basename}");
			if(!$return) {
				HTML::$script .= ' parent.window.alert("Can\'t move uploaded image");';
			}
		}
		
		$id = Data::new_project($_POST["title"], $basename, $_POST["text1"], $_POST["text2"]);
		if(!is_int($id)) {
			trigger_error("Can't insert 'project' to database", E_USER_WARNING);
			return(false);
		}
		
		return($id);
	}//}}}
	
	function save_project()
	{//{{{
		if(!(
			@is_string($_POST["id"])
			&& ctype_digit($_POST["id"])
			&& @is_string($_POST["title"])
			&& @is_string($_POST["text1"])
			&& @is_string($_POST["text2"])
		)) {
			if(defined('DEBUG') && DEBUG) var_dump(['$_POST' => $_POST]);
			trigger_error("Incorrect incoming data", E_USER_WARNING);
			return(false);
		}
		
		$basename = '';
		if(@is_array($_FILES["image"]) && @$_FILES["image"]["error"] === 0) {
		
			$extension = pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION);
			$filename = pathinfo($_FILES["image"]["tmp_name"], PATHINFO_BASENAME);
			
			$basename = "{$filename}.{$extension}";
			$return = rename($_FILES["image"]["tmp_name"], __DIR__."/".self::UPLOAD_DIR."/{$basename}");
			if(!$return) {
				HTML::$script .= ' parent.window.alert("Can\'t move uploaded image");';
			}
		}
		
		$id = Data::save_project(intval($_POST["id"]), $_POST["title"], $basename, $_POST["text1"], $_POST["text2"]);
		if(!is_int($id)) {
			trigger_error("Can't update 'project' on database", E_USER_WARNING);
			return(false);
		}
		
		return($id);
	}//}}}
	
	function delete_project()
	{//{{{
		if(!(
			@is_string($_POST["id"])
			&& ctype_digit($_POST["id"])
		)) {
			if(defined('DEBUG') && DEBUG) var_dump(['$_POST' => $_POST]);
			trigger_error("Incorrect incoming data", E_USER_WARNING);
			return(false);
		}
		
		$id = Data::delete_project(intval($_POST["id"]));
		if(!is_int($id)) {
			trigger_error("Can't delete project in database", E_USER_WARNING);
			return(false);
		}
		
		return($id);
	}//}}}
	
	function save_about()
	{//{{{
		if(!(
			@is_string($_POST["title"])
			&& @is_string($_POST["text1"])
			&& @is_string($_POST["text2"])
		)) {
			if(defined('DEBUG') && DEBUG) var_dump(['$_POST' => $_POST]);
			trigger_error("Incorrect incoming data", E_USER_WARNING);
			return(false);
		}
		
		$basename = '';
		if(@is_array($_FILES["image"]) && @$_FILES["image"]["error"] === 0) {
		
			$extension = pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION);
			$filename = pathinfo($_FILES["image"]["tmp_name"], PATHINFO_BASENAME);
			
			$basename = "{$filename}.{$extension}";
			$return = rename($_FILES["image"]["tmp_name"], __DIR__."/".self::UPLOAD_DIR."/{$basename}");
			if(!$return) {
				HTML::$script .= ' parent.window.alert("Can\'t move uploaded image");';
			}
		}
		
		$return = Data::put_about($_POST["title"], $basename, $_POST["text1"], $_POST["text2"]);
		if(!$return) {
			trigger_error("Can't put 'about' to database", E_USER_WARNING);
			return(false);
		}
		
		return(true);
	}//}}}
	
	function save_contacts()
	{//{{{
		if(!(
			@is_string($_POST["vk"])
			&& @is_string($_POST["telegram"])
			&& @is_string($_POST["email"])
		)) {
			if(defined('DEBUG') && DEBUG) var_dump(['$_POST' => $_POST]);
			trigger_error("Incorrect input contacts data", E_USER_WARNING);
			return(false);
		}
		
		$return = Data::put_contacts($_POST["vk"], $_POST["telegram"], $_POST["email"]);
		if(!$return) {
			trigger_error("Can't insert contacts", E_USER_WARNING);
			return(false);
		}
		return(true);
	}//}}}

	
	function create_tables()
	{//{{{
		$return = Data::create_tables();
		if(!$return) {
			trigger_error("Can't create tables in database", E_USER_WARNING);
			return(false);
		}
		return(true);
	}//}}}
	

	function get_projects()
	{//{{{
		$projects = Data::select_projects();
		if(!is_array($projects)) {
			trigger_error("Can't get projects from database", E_USER_WARNING);
			return(false);
		}
		return($projects);
	}//}}}

	function get_project(int $id)
	{//{{{
		$project = Data::select_project($id);
		if(!is_array($project)) {
			trigger_error("Can't get project from database", E_USER_WARNING);
			return(false);
		}
		return($project);
	}//}}}
	
	function get_about()
	{//{{{
		$about = Data::get_about();
		if(!is_array($about)) {
			trigger_error("Can't get about from database", E_USER_WARNING);
			return(false);
		}
		return($about);
	}//}}}
	
	function get_contacts()
	{//{{{
		$contacts = Data::get_contacts();
		if(!is_array($contacts)) {
			trigger_error("Can't get contacts from database", E_USER_WARNING);
			return(false);
		}
		return($contacts);
	}//}}}

}
$Main = new Main();

