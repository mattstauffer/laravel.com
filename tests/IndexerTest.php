<?php

use App\Services\Documentation\Hit;
use App\Services\Documentation\Indexer;
use Mockery as M;

class IndexerTest extends TestCase {

	public function tearDown()
	{
		m::close();
	}

	private function correctParams()
	{
		return [
			'index' => 'docs.master',
			'body' => [
				'slug' => 'artisan',
				'title' => 'Artisan CLI',
				'body.md' => file_get_contents(__DIR__ . '/stubs/indexer/artisan.md'),
				// Weirdly, can't get stubs to drop the end line
				'body.html' => rtrim(file_get_contents(__DIR__ . '/stubs/indexer/artisan.html'), "\n"),
				'body.highlighting' => rtrim(file_get_contents(__DIR__ . '/stubs/indexer/artisan.highlighting'), "\n")
			],
			'type' => 'page',
			'id' => md5('artisan')
		];
	}

	public function test_it_creates_indexing_params_array_correctly()
	{
		$client = M::mock('Elasticsearch\Client');
		$client->shouldReceive('index')
			->with($this->correctParams())
			->once();

		$markdown = new ParsedownExtra;
		$filesystem = app('Illuminate\Filesystem\Filesystem');

		$indexer = new Indexer($client, $markdown, $filesystem);

		$version = 'master';
		$path = __DIR__ . '/stubs/indexer/artisan.md';

		$indexer->indexDocument($version, $path);
	}

	public function test_it_converts_markdown_for_highlighting_correctly()
	{
		$this->markTestIncomplete(
			'This test has not been implemented yet.'
		);
	}

	public function test_it_converts_slug_from_path_correctly()
	{
		$client = M::mock('Elasticsearch\Client');
		$markdown = new ParsedownExtra;
		$filesystem = app('Illuminate\Filesystem\Filesystem');

		$indexer = new Indexer($client, $markdown, $filesystem);

		$this->assertEquals(
			'artisan',
			$indexer->getSlugFromPath('/home/code/laravel.com/resource/docs/master/artisan.md')
		);
	}

}
