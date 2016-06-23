<?php

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Exception\RequestException;
use ProteusThemes\TfComments\EnvatoApi;

class EnvatoApiTest extends PHPUnit_Framework_TestCase {
	protected $itemsMock;

	public function setUp() {
		date_default_timezone_set( 'UTC' );
	}

	public function testGetItemIdsByAuthor() {
		$handler   = HandlerStack::create( new MockHandler( [
			new Response( 200, [ 'content-type' => 'application/json' ], file_get_contents( __DIR__ . '/fixtures/list-of-themes.json' ) ),
		] ) );
		$envatoApi = new EnvatoApi( '', $handler );

		$expected = [ 6779086, 9323981, 15194530 ];
		$actual = $envatoApi->getItemIdsByAuthor();

		$this->assertEquals( $expected, $actual );
	}

	public function testGetLastCommentsByItemId() {
		$handler = HandlerStack::create( new MockHandler( [
			new Response( 200, [ 'content-type' => 'application/json' ], file_get_contents( __DIR__ . '/fixtures/list-of-comments.json' ) ),
		] ) );
		$envatoApi = new EnvatoApi( '', $handler );

		$expected = [
			[
				'comment_id'      => 13508762,
				'item_id'         => 9323981,
				'username'        => '2simplesolutions',
				'url'             => 'http://themeforest.net/item/buildpress-construction-business-wp-theme/9323981/comments',
				'created_at'      => '2016-06-17T02:56:54+10:00',
				'last_comment_at' => '2016-06-17T16:07:07+10:00',
				'participants'    => ['2simplesolutions', 'ProteusSupport'],
			],
			[
				'comment_id'      => 9957983,
				'item_id'         => 9323981,
				'username'        => 'rbezruchuk',
				'url'             => 'http://themeforest.net/item/buildpress-construction-business-wp-theme/9323981/comments',
				'created_at'      => '2015-05-10T15:44:10+10:00',
				'last_comment_at' => '2016-06-16T17:52:19+10:00',
				'participants'    => ['rbezruchuk', 'ProteusSupport', 'ProteusThemes'],
			],
			[
				'comment_id'      => 13490202,
				'item_id'         => 9323981,
				'username'        => 'oddessit',
				'url'             => 'http://themeforest.net/item/buildpress-construction-business-wp-theme/9323981/comments',
				'created_at'      => '2016-06-15T05:47:19+10:00',
				'last_comment_at' => '2016-06-15T07:18:36+10:00',
				'participants'    => ['oddessit', 'ProteusThemes'],
			],
		];
		$actual = $envatoApi->getLastCommentsByItemId( 9323981 );

		$this->assertEquals( $expected, $actual );
	}
}
