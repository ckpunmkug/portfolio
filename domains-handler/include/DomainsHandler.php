<?php

class DomainsHandler
{
	private $domain = NULL;
	private $deny = NULL;
	private $zones = NULL;
	private $ip = NULL;
	
	public function __construct()
	{//{{{//
		$return = is_array(CONFIG["domains"]["deny"]);
		if(!$return) {//{{{//
			trigger_error('Incorrect CONFIG["domains"]["deny"]', E_USER_ERROR);
			exit(255);
		}//}}}//
		$this->deny = CONFIG["domains"]["deny"];
	
		$return = is_string(CONFIG["domains"]["zones"]);
		if(!$return) {//{{{//
			trigger_error('Incorrect CONFIG["domains"]["zones"]', E_USER_ERROR);
			exit(255);
		}//}}}//
		$this->zones = CONFIG["domains"]["zones"];
	
		$return = is_string(CONFIG["domains"]["ip"]);
		if(!$return) {//{{{//
			trigger_error('Incorrect CONFIG["domains"]["ip"]', E_USER_ERROR);
			exit(255);
		}//}}}//
		$this->ip = CONFIG["domains"]["ip"];
		
		return(true);
	}//}}}//

	public function set_domain(string $domain)
	{//{{{//
		$return = strlen($domain);
		if($return > 255) {//{{{//
			trigger_error("Length of domain name string out of range", E_USER_WARNING);
			return(false);
		}//}}}//
	
		$return = preg_match('/^[a-zA-Z0-9\-]+\.[a-zA-Z]+$/', $domain);
		if($return != 1) {//{{{//
			trigger_error("Incorrect string for domain name level 2", E_USER_WARNING);
			return(false);
		}//}}}//
		
		$domain = strtolower($domain);
		
		$return = in_array($domain, $this->deny);
		if($return) {//{{{//
			trigger_error("'domain' denided for using", E_USER_WARNING);
			return(false);
		}//}}}//
		
		$this->domain = $domain;
		
		return(true);
	}//}}}//

	public function delete_domain_data()
	{//{{{//
		$domain = $this->domain;
		$zones = $this->zones;
		
		self::nginx_disable($domain);
		self::certbot_delete($domain);
		self::rndc_delete($domain, $zones);
		self::delete_domain($domain);
		
		return(true);
	}//}}}//

	private static function nginx_disable(string $domain)
	{//{{{//
		$file = "/etc/nginx/sites-enabled/{$domain}";
		$return = unlink($file);
		if(!$return) {//{{{//
			trigger_error("Can't unlink 'site' file", E_USER_WARNING);
		}//}}}//
		
		$command = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
/usr/bin/systemctl reload nginx
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		$status = 0;
		$return = system($command, $status);
		if($status != 0) {//{{{//
			trigger_error("'reload nginx' failed", E_USER_WARNING);
		}//}}}//
	
		return(NULL);
	}//}}}//

	private static function certbot_delete(string $domain)
	{//{{{//
		$command = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
/usr/bin/certbot -n delete --cert-name {$domain}
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		$status = 0;
		$return = system($command, $status);
		if($status != 0) {//{{{//
			trigger_error("'certbot delete' failed", E_USER_WARNING);
		}//}}}//
		
		return(NULL);
	}//}}}//

	private static function rndc_delete(string $domain, string $zones)
	{//{{{//
		$command = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
/usr/sbin/rndc -s 127.0.0.1 delzone -clean {$domain}
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		$status = 0;
		$return = system($command, $status);
		if($status != 0) {//{{{//
			trigger_error("'rndc delete' failed", E_USER_WARNING);
		}//}}}//
	
		$file = "{$zones}/{$domain}";
		$return = unlink($file);
		if(!$return) {//{{{//
			trigger_error("Can't unlink 'zone' file", E_USER_WARNING);
		}//}}}//
	
		return(NULL);
	}//}}}//
	
	private static function delete_domain(string $domain)
	{//{{{//
		$DB = new DB;
		$domain = $DB->escape($domain);
		$sql = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
DELETE FROM `domains` WHERE `domain`='{$domain}';
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		$return = $DB->query($sql);
		if(!$return) {//{{{//
			trigger_error("Can't perform database query", E_USER_WARNING);
		}//}}}//
		
		return(NULL);
	}//}}}//
	
	public static function create_table()
	{//{{{//
		$DB = new DB;
		
		$sql = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
DROP TABLE IF EXISTS `domains`;
CREATE TABLE `domains` (
	`id` INT AUTO_INCREMENT,
	`domain` TEXT,
	`status` INT DEFAULT 0,
	`expires` DATE DEFAULT NULL,
	PRIMARY KEY(`id`)
);
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		$return = $DB->queries($sql);
		if(!$return) {//{{{//
			//if (defined('DEBUG') && DEBUG) var_dump(['' => ]);
			trigger_error("Can't perform database query", E_USER_WARNING);
			return(false);
		}//}}}//
		
		return(true);
	}//}}}//

	public static function get_domains(int $status = NULL)
	{//{{{//
		$DB = new DB;
		$append = '';
		if($status !== NULL) {
			$append = ' WHERE `status`='.$DB->int($status);
		}
		$sql = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
SELECT * FROM `domains`{$append};
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		$return = $DB->query($sql);
		if(!is_array($return)) {//{{{//
			trigger_error("Can't perform database query", E_USER_WARNING);
			return(false);
		}//}}}//
		$domains = $return;
		
		return($domains);
	}//}}}//

	public function get_domain_data()
	{//{{{//
		$domain = $this->domain;
		$zones = $this->zones;
		$ip = $this->ip;
		
		$return = self::rndc_add($zones, $domain, $ip);
		if(!$return) {//{{{//
			self::update_status($domain, -1);
			return(false);
		}//}}}//
		
		$return = self::nslookup_check($domain, $ip);
		if(!$return) {//{{{//
			self::update_status($domain, -2);
			return(false);
		}//}}}//
		
		system('/usr/bin/systemctl stop nginx');
		system('/usr/bin/systemctl stop apache2');
		
		$return = self::certbot_get($domain);
		
		system('/usr/bin/systemctl start nginx');
		system('/usr/bin/systemctl start apache2');
		
		if(!$return) {//{{{//
			self::update_status($domain, -3);
			return(false);
		}//}}}//
		
		$return = self::nginx_enable($domain, $ip);
		if(!$return) {//{{{//
			self::update_status($domain, -4);
			return(false);
		}//}}}//
		
		self::update_status($domain, 1);
		return(true);
	}//}}}//

	private static function update_status(string $domain, int $status)
	{//{{{//
		$DB = new DB;
		$domain = $DB->escape($domain);
		$status = $DB->int($status);
		$sql = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
UPDATE `domains` SET `status`={$status} WHERE `domain`='{$domain}';
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		$return = $DB->query($sql);
		if(!$return) {//{{{//
			trigger_error("Can't perform database query", E_USER_WARNING);
			return(false);
		}//}}}//
		
		return(true);
	}//}}}//

	private static function rndc_add(string $dir, string $domain, string $ip)
	{//{{{//
		$file = "{$dir}/{$domain}";
		
		$contents = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
\$TTL	604800
@	IN	SOA	{$domain}. root.{$domain}. (
			      2		; Serial
			 604800		; Refresh
			  86400		; Retry
			2419200		; Expire
			 604800 )	; Negative Cache TTL
;
@	IN	NS	ns1.x-super.ru.
@	IN	NS	ns2.x-super.ru.
@	IN	A	{$ip}
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		$return = file_put_contents($file, $contents);
		if(!is_int($return)) {//{{{//
			trigger_error("Can't put contents to zone file", E_USER_WARNING);
			return(false);
		}//}}}//
		
		$command = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
/usr/sbin/rndc -s 127.0.0.1 addzone {$domain} '{type primary; file "{$file}";};'
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		$output = [];
		$status = 0;
		$return = exec($command, $output, $status);
		if($status != 0) {//{{{//
			trigger_error("Execution 'rndc add' failed", E_USER_WARNING);
			return(false);
		}//}}}//
		
		return(true);
	}//}}}//

	private static function nslookup_check(string $domain, string $ip)
	{//{{{//
		$command = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
/usr/bin/nslookup {$domain}
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		$output = [];
		$status = 0;
		$return = exec($command, $output, $status);
		if($status != 0) {//{{{//
			trigger_error("Exec 'nslookup domain' failed", E_USER_WARNING);
			return(false);
		}//}}}//
		
		foreach($output as $string) {
			if(preg_match('/^Address:\s+(.+)$/', $string, $MATCH) == 1) {
				if($MATCH[1] == $ip) return(true);
			}
		}
		
		trigger_error("Lookup address equal passed 'ip' not found", E_USER_WARNING);
		return(false);
	}//}}}//

	private static function certbot_get(string $domain)
	{//{{{//
		$command = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
/usr/bin/certbot certonly -n --standalone --register-unsafely-without-email --no-eff-email --agree-tos -d {$domain}
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		$output = [];
		$status = 0;
		$return = exec($command, $output, $status);
		if($status != 0) {//{{{//
			trigger_error("Exec 'certbot get' failed", E_USER_WARNING);
			return(false);
		}//}}}//
		
		foreach($output as $string) {
			$string = trim($string);
			if(preg_match('/^.+expires\s+on\s+(\d+\-\d+\-\d+)\.$/', $string, $MATCH) == 1) {
				$expires = $MATCH[1];
				
				$return = self::update_expires($domain, $expires);
				if(!$return) {//{{{//
					trigger_error("'certbot get' can't update expires for domain", E_USER_WARNING);
					return(false);
				}//}}}//
				
				return(true);
			}
		}
		
		trigger_error("'certbot get' expires date not found", E_USER_WARNING);
		return(false);
	}//}}}//

	private static function nginx_enable(string $domain, string $ip)
	{//{{{//
		$file = "/etc/nginx/sites-enabled/{$domain}";
		
		$contents = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
server {
	root /var/www/html;
    server_name {$domain};
	location / {
		try_files \$uri \$uri/ =404;
	}
    listen {$ip}:443 ssl;
    ssl_certificate /etc/letsencrypt/live/{$domain}/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/{$domain}/privkey.pem;
    include /etc/letsencrypt/options-ssl-nginx.conf;
    ssl_dhparam /etc/letsencrypt/ssl-dhparams.pem;
}
server {
    if (\$host = {$domain}) {
        return 301 https://\$host\$request_uri;
    }
	listen {$ip}:80 ;
    server_name {$domain};
    return 404;
}
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		$return = file_put_contents($file, $contents);
		if(!is_int($return)) {//{{{//
			trigger_error("Can't put contents to site file", E_USER_WARNING);
			return(false);
		}//}}}//
		
		$command = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
/usr/bin/systemctl reload nginx
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		$output = [];
		$status = 0;
		$return = exec($command, $output, $status);
		if($status != 0) {//{{{//
			trigger_error("Execution 'nginx reload' failed", E_USER_WARNING);
			return(false);
		}//}}}//
		
		return(true);
	}//}}}//

	private static function update_expires(string $domain, string $expires)
	{//{{{//
		$DB = new DB;
		$domain = $DB->escape($domain);
		$expires = $DB->escape($expires);
		$sql = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
UPDATE `domains` SET `expires`='{$expires}' WHERE `domain`='{$domain}';
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		$return = $DB->query($sql);
		if(!$return) {//{{{//
			trigger_error("Can't perform database query", E_USER_WARNING);
			return(false);
		}//}}}//
		
		return(true);
	}//}}}//

	public function insert_domain()
	{//{{{//
		$domain = $this->domain;
		
		$return = self::domain_exists($domain);
		if($return) {//{{{//
			trigger_error("Domain already exists", E_USER_WARNING);
			return(false);
		}//}}}//
		
		$DB = new DB;
		$domain = $DB->escape($domain);
		$sql = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
INSERT INTO `domains` (`domain`) VALUES ('{$domain}');
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		$return = $DB->query($sql);
		if(!$return) {//{{{//
			trigger_error("Can't perform database query", E_USER_WARNING);
			return(false);
		}//}}}//
		
		$id = $DB->id();
		return($id);
	}//}}}//

	private static function domain_exists(string $domain)
	{//{{{//
		$DB = new DB;
		$domain = $DB->escape($domain);
		$sql = 
///////////////////////////////////////////////////////////////{{{//
<<<HEREDOC
SELECT * FROM `domains` WHERE `domain`='${domain}';
HEREDOC;
///////////////////////////////////////////////////////////////}}}//
		$return = $DB->query($sql);
		if(!is_array($return)) {//{{{//
			trigger_error("Can't perform database query", E_USER_WARNING);
			return(false);
		}//}}}//
		
		if(empty($return)) return(false);
		
		return(true);
	}//}}}//

}

