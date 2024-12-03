<?php

define('QUIET', true);

require_once(__DIR__.'/../include/security_init.php');
require_once(__DIR__.'/../include/http_init.php');
require_once(__DIR__.'/../config.php');
require_once(__DIR__.'/../include/DB.php');
require_once(__DIR__.'/../include/HTML.php');
require_once(__DIR__.'/../include/db_init.php');
require_once(__DIR__.'/../include/DomainsHandler.php');
require_once(__DIR__.'/../include/array_get_type.php');
require_once(__DIR__.'/../include/Main.php');

$Main = new Main();
