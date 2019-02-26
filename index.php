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
$unansweredComments = $envatoApi->getAllUnansweredQuestionsByUsers( [ 'ProteusThemes', 'ProteusSupport' ] );
$unansweredCommentsNx = $envatoApi->getAllUnansweredQuestionsByUsers( [ 'ProteusThemesNX', 'ProteusSupport', 'ProteusThemes' ], 'ProteusThemesNX' );

$unansweredComments = array_merge( $unansweredComments, $unansweredCommentsNx );

usort( $unansweredComments, function( $comment1, $comment2 ) {
	$time1 = strtotime( $comment1['last_comment_at'] );
	$time2 = strtotime( $comment2['last_comment_at'] );

	if ( $time1 === $time2 ) {
		return 0;
	}

	return $time1 > $time2 ? -1 : 1;
} );

$unansweredCommentsLastMonth = array_filter( $unansweredComments, function( $comment ) {
	$time = strtotime( $comment['last_comment_at'] );

	return $time > ( time() - 31 * 24 * 60 * 60 ); // one month approx.
} );

if ( empty( $unansweredCommentsLastMonth ) ) {
	die( 'Everything replied, good job captain!' );
}

echo <<<EOH
<html>
<head>
<title>Comments on ThemeForest to be answered</title>
<style>
body {font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol"; line-height: 1.4; color: #555; font-size: 120%}
li {margin-bottom: 5px;}
</style>
</head>
<body>
<h1>Comments on ThemeForest to be answered:</h1>
<ul>
EOH;

foreach ( $unansweredCommentsLastMonth as $comment ) {
	printf(
		'<li>User <a href="http://themeforest.net/user/%2$s">%2$s</a> commented on <a href="http://themeforest.net/comments/%1$s">%3$s</a> (%4$s)</li>',
		$comment['comment_id'],
		$comment['username'],
		date( 'D, jS M \a\t g:i a', strtotime( $comment['last_comment_at'] ) ),
		$comment['item_name']
	);
}

echo <<<EOH
</ul>
</body>
</html>
EOH;
