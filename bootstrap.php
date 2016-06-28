<?php

require_once 'vendor/autoload.php';

$dotenv = new Dotenv\Dotenv(__DIR__);

try {
	$dotenv->load();
} catch ( Dotenv\Exception\InvalidPathException $e ) {
	// do nothing
}

$dotenv->required( [
	'ENVATO_SECRET_TOKEN',
] );
