<?php

define('CONFIG', [
	"database" => [
		"host" => 'localhost',
		"user" => 'domains_handler',
		"password" => 'Twog108GletBid',
		"database" => 'domains_handler',
	],
	"domains" => [
		"deny" => [
			'x-super.ru',
		],
		"zones" => '/etc/bind/zones',
		"ip" => '94.131.113.28',
	],
] );

