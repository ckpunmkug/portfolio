<?php

class Main
{
	function __construct()
	{//{{{
		$request_method = array_get_string('REQUEST_METHOD', $_SERVER);
		if(!is_string($request_method)) {
			trigger_error("Can't get http request method", E_USER_WARNING);
			return(false);
		}
		
		switch($request_method) {
			case('GET'):
				$return = $this->handle_get_request();
				if($return !== true) {
					trigger_error("Handle get request failed", E_USER_ERROR);
					exit(255);
				}
				$HTML = new HTML;
				exit(0);
				
			case('POST'):
				$return = $this->handle_post_request();
				if($return !== true) {
					trigger_error("Handle post request failed", E_USER_ERROR);
					exit(255);
				}
				$HTML = new HTML;
				exit(0);
				
			default:
				if(defined('DEBUG') && DEBUG) var_dump(['$request_method' => $request_method]);
				trigger_error("Unsupported request method", E_USER_ERROR);
				exit(255);
		}
	}//}}}
	
	function handle_get_request()
	{//{{{
		$page = @strval($_GET["page"]);
		if($page == 'list') {
			$this->list();
		}
		else {
			$this->index();
		}
		return(true);
	}//}}}
	
	function handle_post_request()
	{//{{{
		$this->add();
		return(true);
	}//}}}

	function index()
	{//{{{//
		$csrf_token = CSRF_TOKEN;
		HTML::$body .= 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
<h4>Загрузить домены</h4>
<form action="/index.php" method="post" enctype="multipart/form-data">
	<input name="csrf_token" value="{$csrf_token}" type="hidden" />
	
	<label>
		Файл со списком
		<input name="file" type="file" />
	</label><br />
	
	<input value="Отправить" type="submit" />
</form>

HEREDOC;
///////////////////////////////////////////////////////////////}}}//
	$domains = DomainsHandler::get_domains();
		$total = count($domains);
		
		$domains = DomainsHandler::get_domains(0);
		$zero = count($domains);
		
		$domains = DomainsHandler::get_domains(1);
		$complete = count($domains);
		
		$error = $total - $zero - $complete;
		
		HTML::$body .= 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
<hr /><h4>Статистика</h4>
Всего доменов в БД - {$total}<br />
Не обработанных - {$zero}<br />
С ошибкой обработки - {$error}<br />
<a href="index.php?page=list">С сертификатом - {$complete}</a><br />

HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		return(true);
	}//}}}//

	function list()
	{//{{{//
		$DOMAIN = DomainsHandler::get_domains(1);
		foreach($DOMAIN as $domain) {
			HTML::$body .= 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
<a href="https://{$domain["domain"]}/">https://{$domain["domain"]}/</a><br />
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		}
		return(true);
	}//}}}//

	function add()
	{//{{{//
		$file = array_get_array('file', $_FILES);
		if(!is_array($file)) {
			trigger_error("Incorrect array 'file' in '_FILES' array", E_USER_WARNING);
			return(false);
		}

		$error = array_get_int('error', $file);
		if(!is_int($error)) {
			trigger_error("Incorrect int 'error' in 'file' array", E_USER_WARNING);
			return(false);
		}
		if($error !== 0) {
			trigger_error("File uploading error", E_USER_WARNING);
			return(false);
		}

		$name = array_get_string('name', $file);
		if(!is_string($name)) {
			trigger_error("Incorrect string 'name' in 'file' array", E_USER_WARNING);
			return(false);
		}

		$tmp_name = array_get_string('tmp_name', $file);
		if(!is_string($tmp_name)) {
			trigger_error("Incorrect string 'tmp_name' in 'file' array", E_USER_WARNING);
			return(false);
		}
		$file = $tmp_name;
		
		$DOMAIN = file($file);
		if(!is_array($DOMAIN)) {//{{{//
			trigger_error("Can't load domains from file", E_USER_ERROR);
			exit(255);
		}//}}}//
		
		$DomainsHandler = new DomainsHandler();
		foreach($DOMAIN as $domain) {
			$domain = trim($domain);
			if(empty($domain)) continue;
			
			$return = $DomainsHandler->set_domain($domain);
			if(!$return) continue;
			
			$return = $DomainsHandler->insert_domain();
			if(!is_int($return)) continue;
		}
		
		$this->index();
		
		return(true);
	}//}}}//

}

