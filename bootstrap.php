<?php

require_once 'vendor/autoload.php';

$dotenv = new Dotenv\Dotenv(__DIR__);
$dotenv->load();
$dotenv->required( [
	'ENVATO_SECRET_TOKEN',
] );
