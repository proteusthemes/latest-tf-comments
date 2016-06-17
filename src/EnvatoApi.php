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
	protected function get( $endpoint ) {
		try {
			$response = $this->client->get( $endpoint, [
				'headers'   => [
					'Authorization' => sprintf( 'Bearer %s', $this->access_token ),
				],
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
			'query' => [
				'username' => $username
			],
		] );

		$out = array_map( function( $item ) {
			return $item->id;
		}, $response->matches );

		return $out;
	}

	public function getAllUnansweredQuestions() {
		$comments = [];

		$itemIds = $this->getItemIdsByAuthor( 'ProteusThemes' );

		foreach ($itemIds as $itemId) {
			$comments += $this->getLastCommentsByItemId( $itemId );
		}

		$unanswered_questions = $this->returnCommentsWithoutParticipants( ['ProteusThemes', 'ProteusSupport'] );

		return $unanswered_questions;
	}

	public function returnCommentsWithoutParticipants( $blackListUsernames = [] ) {
		# code...
	}

	public function getLastCommentsByItemId( $itemId ) {
		# code...
	}
}
