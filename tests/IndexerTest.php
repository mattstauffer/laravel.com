<?php

use App\Services\Documentation\Hit;

class IndexerTest extends TestCase {

	private $correctParams = [
		'index' => '',
		'body' => [
			'slug' => '',
			'title' => '',
			'body.md' => '',
			'body.html' => '',
			'body.highlighting' => ''
		],
		'type' => 'page',
		'id' => ''
	];

	public function test_it_creates_indexing_params_array_correctly()
	{
		$version = 'master';
		$path = '/home/vagrant/code/laravel.com/resources/docs/master/artisan.md';

//		$client = M::mock('Elasticsearch\Client');
		$markdown = new ParsedownExtra;
		$filesystem = app('Illuminate\Filesystem\Filesystem');

//		$indexer = new \App\Services\Documentation\Indexer($client, $markdown, $filesystem);

		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	public function test_it_converts_markdown_for_highlighting_correctly()
	{


		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}
}
