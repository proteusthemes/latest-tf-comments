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

	return $time > ( time() - 31*24*60*60 ); // one month approx.
} );

if ( empty( $unansweredCommentsLastMonth ) ) {
	die( 'Everything replied, good job captain!' );
}

echo '<ul>';
foreach ( $unansweredCommentsLastMonth as $comment ) {
	printf( '<li><a href="http://themeforest.net/comments/%1$s">Comment</a> by <a href="http://themeforest.net/user/%2$s">%2$s</a>  at %3$s</li>', $comment['comment_id'], $comment['username'], $comment['last_comment_at'] );
}
echo '</ul>';
