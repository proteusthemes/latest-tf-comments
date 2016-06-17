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
			new Response( 200, [ 'content-type' => 'application/json' ], '{
					"took": 6,
					"matches": [
						{
							"id": 6779086,
							"name": "Organique - HTML Template For Healthy Food Store"
						},
						{
							"id": 9323981,
							"name": "BuildPress - Construction Business WP Theme"
						},
						{
							"id": 15194530,
							"name": "Auto - Ideal Car Mechanic and Auto Repair Template for WordPress"
						}
					],
					"timed_out": false,
					"total_hits": 3,
					"links": {
						"next_page_url": null,
						"prev_page_url": null,
						"first_page_url": "https://api.envato.com/v1/discovery/search/search/item?page=1&username=proteusthemes",
						"last_page_url": "https://api.envato.com/v1/discovery/search/search/item?page=1&username=proteusthemes"
					},
					"author_exists": null
				}'
			),
		] ) );
		$envatoApi = new EnvatoApi( '', $handler );

		$expected = [6779086, 9323981, 15194530];
		$actual = $envatoApi->getItemIdsByAuthor();

		$this->assertEquals( $expected, $actual );
	}

	public function testGetLastCommentsByItemId() {
		$handler   = HandlerStack::create( new MockHandler( [
			new Response( 200, [ 'content-type' => 'application/json' ], '{
					"matches": [
						{
							"id": 13508762,
							"item_id": "9323981",
							"item_name": "BuildPress - Construction Business WP Theme",
							"url": "http://themeforest.net/item/buildpress-construction-business-wp-theme/9323981/comments",
							"last_comment_at": "2016-06-17T16:07:07+10:00",
							"conversation": [
								{
									"id": 13508762,
									"username": "2simplesolutions",
									"content": "Hi,\r\n\r\nCan you make the project image gallery into a slider and not displaying all the images down the right hand side??\r\n\r\nThanks",
									"created_at": "2016-06-17T02:56:54+10:00",
									"author_comment": false,
									"hidden_by_complaint": false,
									"complaint_state": "no_complaint",
									"profile_image_url": null
								},
								{
									"id": 13513133,
									"username": "ProteusSupport",
									"content": "Out of the box, Unfortunately no.\r\n\r\nIt can be done but with customization of the theme. If you are interested in costumization please write to us on http://support.proteusthemes.com/",
									"created_at": "2016-06-17T16:07:07+10:00",
									"author_comment": false,
									"hidden_by_complaint": false,
									"complaint_state": "no_complaint",
									"profile_image_url": "https://0.s3.envato.com/files/116512625/avatar_lite.png"
								}
							]
						},
						{
							"id": 9957983,
							"item_id": "9323981",
							"item_name": "BuildPress - Construction Business WP Theme",
							"url": "http://themeforest.net/item/buildpress-construction-business-wp-theme/9323981/comments",
							"last_comment_at": "2016-06-16T17:52:19+10:00",
							"conversation": [
								{
									"id": 9957983,
									"username": "rbezruchuk",
									"content": "Hi, I think I like your theme, but I have a question. There are testimonials on the home page, I need my clients to be able to post their testimonials on their own. Will it be possible for them?",
									"created_at": "2015-05-10T15:44:10+10:00",
									"author_comment": false,
									"hidden_by_complaint": false,
									"complaint_state": "no_complaint",
									"profile_image_url": null
								},
								{
									"id": 9964087,
									"username": "ProteusSupport",
									"content": "Hi,\r\n\r\nyes, your clients will be able to post/add/edit testimonials :)",
									"created_at": "2015-05-11T16:56:12+10:00",
									"author_comment": false,
									"hidden_by_complaint": false,
									"complaint_state": "no_complaint",
									"profile_image_url": "https://0.s3.envato.com/files/116512625/avatar_lite.png"
								},
								{
									"id": 13504536,
									"username": "ProteusThemes",
									"content": "To clarify this answer (as recently some people interpreted it incorrectly): we were referring that any user with access to wp-admin can post and edit testimonials. We understood this situation: user rbezruchuk is a developer and he is setting up a website for the end client (construction business) - he is interested in if the testimonials are easy enough for the end client (construction business owner) to change them later on, without knowledge how to code.\r\n\r\nThere is no special functionality in the theme that would allow you to give access for editing a single testimonial on the frontend directly.",
									"created_at": "2016-06-16T17:52:19+10:00",
									"author_comment": true,
									"hidden_by_complaint": false,
									"complaint_state": "no_complaint",
									"profile_image_url": "https://0.s3.envato.com/files/83661947/avatar_lite.png"
								}
							]
						},
						{
							"id": 13490202,
							"item_id": 9323981,
							"item_url": "http://themeforest.net/item/buildpress-construction-business-wp-theme/9323981",
							"item_name": "BuildPress - Construction Business WP Theme",
							"url": "http://themeforest.net/item/buildpress-construction-business-wp-theme/9323981/comments",
							"site": "themeforest.net",
							"item_author_id": 2194457,
							"item_author_url": "http://themeforest.net/user/ProteusThemes",
							"last_comment_at": "2016-06-15T07:18:36+10:00",
							"conversation": [
								{
									"id": 13490202,
									"username": "oddessit",
									"content": "Hi\r\nhow edit page with visual composer?\r\nnow work you builder, but in the description of the theme written that we can work with visual composer",
									"created_at": "2016-06-15T05:47:19+10:00",
									"author_comment": false,
									"hidden_by_complaint": false,
									"complaint_state": "no_complaint",
									"profile_image_url": null
								},
								{
									"id": 13490724,
									"username": "ProteusThemes",
									"content": "Hi, thank you very much for your comment, but please understand we can offer timely support only on our support platform. Please check http://support.proteusthemes.com/ - you",
									"created_at": "2016-06-15T07:04:03+10:00",
									"author_comment": true,
									"hidden_by_complaint": false,
									"complaint_state": "no_complaint",
									"profile_image_url": "https://0.s3.envato.com/files/83661947/avatar_lite.png"
								},
								{
									"id": 13490835,
									"username": "oddessit",
									"content": "thanks for answer ",
									"created_at": "2016-06-15T07:18:36+10:00",
									"author_comment": false,
									"hidden_by_complaint": false,
									"complaint_state": "no_complaint",
									"profile_image_url": null
								}
							],
							"total_converstations": 3,
							"buyer_and_author": true,
							"highlightable": [
								"13490202: Hi\r\nhow edit page with visual composer?\r\nnow work you builder, but in the description of the theme written that we can work with visual composer",
								"13490724: Hi, thank you very much for your comment, but please understand we can offer timely support only on our support platform. Please check http://support.proteusthemes.com/",
								"13490835: thanks for answer "
							]
						},
					]
				}'
			),
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
				'participants'    => ['2simplesolutions', 'ProteusSupport'],
			],

		];
		$actual = $envatoApi->getLastCommentsByItemId( 9323981 );

		$this->assertEquals( $expected, $actual );
	}
}
