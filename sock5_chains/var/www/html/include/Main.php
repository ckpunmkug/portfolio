<?php

class Main
{
	var $url_path = '';
	var $csrf_token = '';

	var $proxychains_config = '/var/www/.proxychains/proxychains.conf';
	var $proxy_line = 5;

	function __construct()
	{//{{{
		$return = get_url_path();
		if(!is_string($return)) {
			trigger_error("Can't get url path", E_USER_ERROR);
		}
		$this->url_path = $return;
	
		if(@is_string(CSRF_TOKEN) !== true) {
			trigger_error("Incorrect constant 'CSRF_TOKEN'", E_USER_ERROR);
		}
		$this->csrf_token = CSRF_TOKEN;
	
		$request_method = @strval($_SERVER["REQUEST_METHOD"]);
		switch($request_method) {
			case('GET'):
				$return = $this->handle_get_request();
				if($return !== true) {
					trigger_error("Handle get request failed", E_USER_ERROR);
				}
				exit(0);
			case('POST'):
				$return = $this->handle_post_request();
				if($return !== true) {
					trigger_error("Handle post request failed", E_USER_ERROR);
				}
				exit(0);
			default:
				trigger_error("Unsupported http request method", E_USER_ERROR);
		}
	}//}}}
	
	function handle_get_request()
	{//{{{
		$page = @strval($_GET["page"]);
		switch($page) {
			case(''):
				$return = $this->main();
				if($return !== true) {
					trigger_error("Can't create 'main' page", E_USER_WARNING);
					return(false);
				}
				return(true);
			default:
				trigger_error("Unsupported 'page'", E_USER_WARNING);
				return(false);
		}
	}//}}}
	
	function handle_post_request()
	{//{{{
		$action = @strval($_POST["action"]);
		switch($action) {
			case('save'):
				$return = $this->save();
				if($return !== true) {
					trigger_error("Can't perform 'save' action", E_USER_WARNING);
					return(false);					
				}
				return(true);
			default:
				trigger_error("Unsupported 'action'", E_USER_WARNING);
				return(false);
		}
	}//}}}
	
	function main()
	{//{{{
		$proxy = $this->get_current_proxy();
		if(!is_array($proxy)) {
			trigger_error("Can't get current proxy", E_USER_WARNING);
			return(false);
		}
	
		$url_path = htmlentities($this->url_path);
		$csrf_token = htmlentities($this->csrf_token);
		
		$checked = ['http'=>'', 'socks4'=>'', 'socks5'=>''];
		$checked[$proxy["type"]] = 'checked';
		
		HTML::$body .= 
////////////////////////////////////////////////////////////////////////////////
<<<HEREDOC
<fieldset>
	<legend>Proxy parameters</legend>
	<form action="{$url_path}" method="post">
		<input name="csrf_token" value="{$csrf_token}" type="hidden" />
		<label>Type</label><br />
		<label>
			<input name="type" value="http" type="radio" {$checked["http"]} />
			http
		</label><br />
		<label>
			<input name="type" value="socks4" type="radio" {$checked["socks4"]} />
			socks4
		</label><br />
		<label>
			<input name="type" value="socks5" type="radio" {$checked["socks5"]} />
			socks5
		</label><br />
		<label>
			IP<br />
			<input name="ip" value="{$proxy['ip']}" type="text" size="24" />
		</label>
		<br />
		<label>
			Port<br />
			<input name="port" value="{$proxy['port']}" type="text" size="24" />
		</label>
		<br />
		<label>
			User<br />
			<input name="user" value="{$proxy['user']}" type="text" size="24" />
		</label>
		<br />
		<label>
			Password<br />
			<input name="password" value="{$proxy['password']}" type="text" size="24" />
		</label>
		<hr />
		<button name="action" value="save" type="submit">Save</button>
	</from>
</fieldset>
HEREDOC;
////////////////////////////////////////////////////////////////////////////////

		return(true);
	}//}}}

	function save()
	{//{{{
		$csrf_token = @strval($_POST['csrf_token']);
		if(strcmp($this->csrf_token, $csrf_token) !== 0) {
			trigger_error("Compare csrf_tokens failed", E_USER_ERROR);
		}
		
		$url_path = htmlentities($this->url_path);
		
		$line = 
			@strval($_POST["type"])."\t".
			@strval($_POST["ip"])."\t".
			@strval($_POST["port"])."\t".
			@strval($_POST["user"])."\t".
			@strval($_POST["password"])
		;
		
		$error_message =
////////////////////////////////////////////////////////////////////////////////
<<<HEREDOC
<h2>Incorrect proxy parameters</h1>
<a href="{$url_path}"><button>Back</button></a>
HEREDOC;
////////////////////////////////////////////////////////////////////////////////
		
		$proxy = $this->parse_proxy_line($line);
		if(!is_array($proxy)) {
			HTML::$body .= $error_message;
			return(true);
		}
		
		$return = $this->set_current_proxy($proxy);
		if(!$return) {
			trigger_error("Can't set current proxy", E_USER_WARNING);
			return(false);
		}
		
		$this->stop_service();
		$this->start_service();
		
		$complete_message =
////////////////////////////////////////////////////////////////////////////////
<<<HEREDOC
<h2>Proxy parameters saved</h1>
<a href="{$url_path}"><button>Back</button></a>
HEREDOC;
////////////////////////////////////////////////////////////////////////////////
		HTML::$body .= $complete_message;	
		
		return(true);
	}//}}}

	function stop_service()
	{//{{{//
		$command = "/usr/bin/sudo /usr/bin/systemctl stop proxychains_with_microsocks";
		$output = [];
		$status = 255;
		$return = exec($command, $output, $status);
		if($status != 0) {
			trigger_error("Can't stop `proxychains_with_microsocks` service", E_USER_WARNING);
			return(false);
		}
		return(true);
	}//}}}//

	function start_service()
	{//{{{//
		$command = "/usr/bin/sudo /usr/bin/systemctl start proxychains_with_microsocks";
		$output = [];
		$status = 255;
		$return = exec($command, $output, $status);
		if($status != 0) {
			trigger_error("Can't start `proxychains_with_microsocks` service", E_USER_WARNING);
			return(false);
		}
		return(true);
	}//}}}//
	
	function get_current_proxy()
	{//{{{//
		$LINE = file($this->proxychains_config);
		if(!is_array($LINE)) {
			trigger_error("Can't open `proxychains` config file", E_USER_WARNING);
			return(false);
		}
		
		$line = trim($LINE[$this->proxy_line]);
		
		$proxy = $this->parse_proxy_line($line);
		if(!is_array($proxy)) return(false);
		
		return($proxy);
	}//}}}//
	
	function set_current_proxy(array $proxy)
	{//{{{//
		$LINE = file($this->proxychains_config);
		if(!is_array($LINE)) {
			trigger_error("Can't open `proxychains` config file", E_USER_WARNING);
			return(false);
		}
		
		$LINE[$this->proxy_line] =  
			$proxy["type"]."\t".
			$proxy["ip"]."\t".
			$proxy["port"]."\t".
			$proxy["user"]."\t".
			$proxy["password"]."\n"
		;
		$contents = implode('', $LINE);
		
		$return = file_put_contents($this->proxychains_config, $contents);
		if(!is_int($return)) {
			trigger_error("Can't put contents to `proxychains` config file", E_USER_WARNING);
			return(false);
		}
		
		return(true);
	}//}}}//
	
	function parse_proxy_line(string $line)
	{//{{{//
		$pattern = '/^'
			.'(http|socks4|socks5)'
			.'\s+([0-9\.]+)'
			.'\s+([0-9]+)'
			.'\s+([a-zA-Z0-9]+)'
			.'\s+([a-zA-Z0-9]+)'
		.'$/';
		
		$return = preg_match($pattern, $line, $MATCH);
		if($return != 1) {
			trigger_error("Can't parse proxy line", E_USER_WARNING);
			return(false);
		}
		
		$result = [
			"type" => $MATCH[1],
			"ip" => $MATCH[2],
			"port" => $MATCH[3],
			"user" => $MATCH[4],
			"password" => $MATCH[5],
		];
		
		return($result);
	}//}}}//
	
}

