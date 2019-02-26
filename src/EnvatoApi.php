<?php
namespace ProteusThemes\TfComments;

use \GuzzleHttp\Client;
use \GuzzleHttp\Exception\RequestException;

/**
* Wrapper for the Envato API
*/
class EnvatoApi  {
	/**
	 * GuzzleHttp client
	 * @var \GuzzleHttp\Client
	 */
	protected $client;

	/**
	 * Envato API access token
	 * @var string
	 */
	protected $access_token = '';

	/**
	 * @var \Monolog\Logger
	 */
	protected $logger;

	public function __construct( $access_token, \GuzzleHttp\HandlerStack $handler = null ) {
		$this->setAccessToken( $access_token );

		$this->client = new Client( [
			'base_uri' => 'https://api.envato.com/',
			'handler'  => $handler,
		] );
	}

	public function setLogger( \Monolog\Logger $logger ) {
		$this->logger = $logger;
	}

	protected function decodeResponse( $response ) {
		return json_decode( $response->getBody()->getContents() );
	}

	public function setAccessToken( $token ) {
		$this->access_token = $token;

		return $this->access_token;
	}

	// GET http request, with predefined $this->client
	protected function get( $endpoint, array $query_params = [] ) {
		try {
			$response = $this->client->get( $endpoint, [
				'headers'   => [
					'Authorization' => sprintf( 'Bearer %s', $this->access_token ),
				],
				'query' => $query_params
			] );
		} catch ( RequestException $e ) {
			$msg = sprintf( 'Error when doing GET to Envato API: %s', $e->getMessage() );

			if ( $this->logger ) {
				$this->logger->addCritical( $msg, $e->getHandlerContext() );
			}

			echo $msg;
		}

		return $this->decodeResponse( $response );
	}

	public function getItemIdsByAuthor( $username = 'ProteusThemes' ) {
		$response = $this->get( '/v1/discovery/search/search/item', [
			'username' => $username
		] );

		$out = array_map( function( $item ) {
			return $item->id;
		}, $response->matches );

		return $out;
	}

	public function getAllUnansweredQuestionsByUsers( array $blackListUsernames, $tfAuthor = 'ProteusThemes' ) {
		$comments = [];

		$itemIds = $this->getItemIdsByAuthor( $tfAuthor );

		foreach ( $itemIds as $itemId ) {
			$comments = array_merge( $comments, $this->getLastCommentsByItemId( $itemId ) );
		}

		$unanswered_questions = $this->getCommentsWithoutLastAnswerByUsers( $comments, $blackListUsernames );

		return $unanswered_questions;
	}

	public function getCommentsWithoutLastAnswerByUsers( array $allComments, array $blackListUsernames ) {
		$out = array_filter( $allComments, function( $comment ) use ( $blackListUsernames ) {
			return ! in_array( $comment['last_reply_by'], $blackListUsernames );
		} );

		$out = array_values( $out );

		return $out;
	}

	public function getLastCommentsByItemId( $itemId ) {
		$response = $this->get( '/v1/discovery/search/search/comment', [
			'item_id'   => $itemId,
			'page_size' => 10,
			'sort_by'   => 'newest',
		] );

		$out = array_map( function( $comment ) {
			$participants = array_map( function( $reply ) {
				return $reply->username;
			}, $comment->conversation );

			$participants = array_unique( $participants );

			return [
				'comment_id'      => intval( $comment->id ),
				'item_id'         => intval( $comment->item_id ),
				'item_name'       => $comment->item_name,
				'username'        => $comment->conversation[0]->username,
				'url'             => $comment->url,
				'created_at'      => $comment->conversation[0]->created_at,
				'last_comment_at' => $comment->last_comment_at,
				'participants'    => $participants,
				'last_reply_by'   => end( $comment->conversation )->username,
			];
		}, $response->matches );

		return $out;
	}
}
