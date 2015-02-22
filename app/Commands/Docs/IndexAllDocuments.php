<?php namespace App\Commands\Docs;

use App\Commands\Command;
use App\Services\Documentation\Indexer;
use Illuminate\Contracts\Bus\SelfHandling;

class IndexAllDocuments extends Command implements SelfHandling {

	/**
	 * Create a new command instance.
	 */
	public function __construct()
	{
	}

	/**
	 * Index all of our local documentation in ElasticSearch
	 *
	 * @param Indexer $indexer
	 */
	public function handle(Indexer $indexer)
	{
		$indexer->indexAllDocuments();
	}
}
