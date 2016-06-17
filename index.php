<?php

require_once 'bootstrap.php';

use \Monolog\Logger;
use \ProteusThemes\TfComments\EnvatoApi;


/**
 * Logger
 */
// $logger = new Logger( 'general' );

/**
 * EnvatoApi instance
 * @var EnvatoApi
 */
$envatoApi = new EnvatoApi( getenv( 'ENVATO_SECRET_TOKEN' ) );
$items = $envatoApi->getItemIds();

echo '<ul>';
foreach ($items as $itemId) {
	printf( '<li>%s</li>', $itemId );
}
echo '</ul>';

die();

/**
 * See https://github.com/zendesk/zendesk_jwt_sso_examples/blob/master/php_jwt.php
 */

$key   = $config['zendesk_shared_secret'];
$now   = time();
$token = [
	'jti'         => md5( $now . mt_rand() ),
	'iat'         => $now,
	'name'        => $EnvatoApi->get_name(),
	'email'       => $EnvatoApi->get_email(),
	'tags'        => [ 'username_' . $EnvatoApi->get_username() ],
	'user_fields' => [
		'bought_themes'    => $EnvatoApi->get_bought_items_string(),
		'supported_themes' => $EnvatoApi->get_supported_items_string(),
		'tf_username'      => $EnvatoApi->get_username(),
		'country'          => $EnvatoApi->get_country(),
	],
];

$jwt = JWT::encode( $token, $key );
$location = sprintf( 'https://%s.zendesk.com/access/jwt?jwt=%s', $config['zendesk_subdomain'], $jwt );

if( ! empty( $_SESSION['zendesk_return_to'] ) ) {
	$location .= sprintf( '&return_to=%s', urlencode( $_SESSION['zendesk_return_to'] ) );
}

// Redirect
if ( 'true' !== getenv( 'ZEL_DEBUG' ) ) {
	header( 'Location: ' . $location );
	exit;
}
else {
	print_r( $token );
}
