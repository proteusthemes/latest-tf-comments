<?php

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Exception\RequestException;
use ProteusThemes\TfComments\EnvatoApi;

class EnvatoApiTest extends \PHPUnit\Framework\TestCase {
	protected $itemsMock;

	protected function setUp(): void {
		date_default_timezone_set( 'UTC' );
	}

	public function testGetItemIdsByAuthor() {
		$handler   = HandlerStack::create( new MockHandler( [
			new Response( 200, [ 'content-type' => 'application/json' ], file_get_contents( __DIR__ . '/fixtures/list-of-themes.json' ) ),
		] ) );
		$envatoApi = new EnvatoApi( '', $handler );

		$expected = [ 9323981, 15194530, 6779086 ];
		$actual = $envatoApi->getItemIdsByAuthor();

		$this->assertEquals( $expected, $actual );
	}

	public function testGetLastCommentsByItemId() {
		$handler = HandlerStack::create( new MockHandler( [
			new Response( 200, [ 'content-type' => 'application/json' ], file_get_contents( __DIR__ . '/fixtures/list-of-comments-all-replies.json' ) ),
		] ) );
		$envatoApi = new EnvatoApi( '', $handler );

		$expected = [
			[
				'comment_id'      => 13508762,
				'item_id'         => 9323981,
				'item_name'       => 'BuildPress - Construction Business WP Theme',
				'username'        => '2simplesolutions',
				'url'             => 'http://themeforest.net/item/buildpress-construction-business-wp-theme/9323981/comments',
				'created_at'      => '2016-06-17T02:56:54+10:00',
				'last_comment_at' => '2016-06-17T16:07:07+10:00',
				'participants'    => [ '2simplesolutions', 'ProteusSupport' ],
				'last_reply_by'   => 'ProteusSupport',
			],
			[
				'comment_id'      => 9957983,
				'item_id'         => 9323981,
				'item_name'       => 'BuildPress - Construction Business WP Theme',
				'username'        => 'rbezruchuk',
				'url'             => 'http://themeforest.net/item/buildpress-construction-business-wp-theme/9323981/comments',
				'created_at'      => '2015-05-10T15:44:10+10:00',
				'last_comment_at' => '2016-06-16T17:52:19+10:00',
				'participants'    => [ 'rbezruchuk', 'ProteusSupport', 'ProteusThemes' ],
				'last_reply_by'   => 'ProteusThemes',
			],
			[
				'comment_id'      => 13490202,
				'item_id'         => 9323981,
				'item_name'       => 'BuildPress - Construction Business WP Theme',
				'username'        => 'oddessit',
				'url'             => 'http://themeforest.net/item/buildpress-construction-business-wp-theme/9323981/comments',
				'created_at'      => '2016-06-15T05:47:19+10:00',
				'last_comment_at' => '2016-06-15T07:18:36+10:00',
				'participants'    => [ 'oddessit', 'ProteusThemes' ],
				'last_reply_by'   => 'oddessit',
			],
		];
		$actual = $envatoApi->getLastCommentsByItemId( 9323981 );

		$this->assertEquals( $expected, $actual );
	}

	public function testGetCommentsWithoutOurLastAnswer() {
		$handler = HandlerStack::create( new MockHandler( [
			new Response( 200, [ 'content-type' => 'application/json' ], file_get_contents( __DIR__ . '/fixtures/list-of-comments-no-replies.json' ) ),
			new Response( 200, [ 'content-type' => 'application/json' ], file_get_contents( __DIR__ . '/fixtures/list-of-comments-some-replies.json' ) ),
		] ) );
		$envatoApi = new EnvatoApi( '', $handler );

		$allCommentsNoReplies   = $envatoApi->getLastCommentsByItemId( 9323981 ); // first mock response
		$allCommentsSomeReplies = $envatoApi->getLastCommentsByItemId( 9323981 ); // second mock response

		$expectedForNoReplies = [
			[
				'comment_id'      => 13508762,
				'item_id'         => 9323981,
				'item_name'       => 'BuildPress - Construction Business WP Theme',
				'username'        => '2simplesolutions',
				'url'             => 'http://themeforest.net/item/buildpress-construction-business-wp-theme/9323981/comments',
				'created_at'      => '2016-06-17T02:56:54+10:00',
				'last_comment_at' => '2016-06-17T16:07:07+10:00',
				'participants'    => [ '2simplesolutions', 'SomeOtherUser' ],
				'last_reply_by'   => 'SomeOtherUser',
			],
			[
				'comment_id'      => 9957983,
				'item_id'         => 9323981,
				'item_name'       => 'BuildPress - Construction Business WP Theme',
				'username'        => 'rbezruchuk',
				'url'             => 'http://themeforest.net/item/buildpress-construction-business-wp-theme/9323981/comments',
				'created_at'      => '2015-05-10T15:44:10+10:00',
				'last_comment_at' => '2016-06-16T17:52:19+10:00',
				'participants'    => [ 'rbezruchuk' ],
				'last_reply_by'   => 'rbezruchuk',
			],
		];
		$expectedForSomeReplies = [
			[
				'comment_id'      => 9957983,
				'item_id'         => 9323981,
				'item_name'       => 'BuildPress - Construction Business WP Theme',
				'username'        => 'rbezruchuk',
				'url'             => 'http://themeforest.net/item/buildpress-construction-business-wp-theme/9323981/comments',
				'created_at'      => '2015-05-10T15:44:10+10:00',
				'last_comment_at' => '2016-06-16T17:52:19+10:00',
				'participants'    => [ 'rbezruchuk' ],
				'last_reply_by'   => 'rbezruchuk',
			],
		];

		$actualForNoReplies = $envatoApi->getCommentsWithoutLastAnswerByUsers( $allCommentsNoReplies, [ 'ProteusThemes', 'ProteusSupport' ] );
		$actualforSomeReplies = $envatoApi->getCommentsWithoutLastAnswerByUsers( $allCommentsSomeReplies, [ 'ProteusThemes', 'ProteusSupport' ] );

		$this->assertEquals( $expectedForNoReplies, $actualForNoReplies, 'All comments without replies' );
		$this->assertEquals( $expectedForSomeReplies, $actualforSomeReplies, 'One comment with our reply and one without' );
	}

	public function testGetAllUnansweredQuestions() {
		$handler = HandlerStack::create( new MockHandler( [
			new Response( 200, [ 'content-type' => 'application/json' ], file_get_contents( __DIR__ . '/fixtures/list-of-themes.json' ) ),
			new Response( 200, [ 'content-type' => 'application/json' ], file_get_contents( __DIR__ . '/fixtures/list-of-comments-some-replies.json' ) ),
			new Response( 200, [ 'content-type' => 'application/json' ], file_get_contents( __DIR__ . '/fixtures/list-of-comments-some-replies2.json' ) ),
			new Response( 200, [ 'content-type' => 'application/json' ], file_get_contents( __DIR__ . '/fixtures/list-of-comments-some-replies3.json' ) ),
		] ) );
		$envatoApi = new EnvatoApi( '', $handler );

		$expected = [
			[
				'comment_id'      => 9957983,
				'item_id'         => 9323981,
				'item_name'       => 'BuildPress - Construction Business WP Theme',
				'username'        => 'rbezruchuk',
				'url'             => 'http://themeforest.net/item/buildpress-construction-business-wp-theme/9323981/comments',
				'created_at'      => '2015-05-10T15:44:10+10:00',
				'last_comment_at' => '2016-06-16T17:52:19+10:00',
				'participants'    => [ 'rbezruchuk' ],
				'last_reply_by'   => 'rbezruchuk',
			],
			[
				'comment_id'      => 13508762,
				'item_id'         => 6779086,
				'item_name'       => 'Organique - HTML Template For Healthy Food Store',
				'username'        => '2simplesolutions',
				'url'             => 'http://themeforest.net/item/organique/6779086/comments',
				'created_at'      => '2016-06-17T02:56:54+10:00',
				'last_comment_at' => '2016-06-17T16:07:07+10:00',
				'participants'    => [ '2simplesolutions' ],
				'last_reply_by'   => '2simplesolutions',
			],
		];

		$actual = $envatoApi->getAllUnansweredQuestionsByUsers( [ 'ProteusThemes', 'ProteusSupport' ] );

		$this->assertEquals( $expected, $actual, 'Unanswered from multiple items' );
	}
}
