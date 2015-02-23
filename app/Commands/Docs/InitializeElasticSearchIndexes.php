<?php namespace App\Commands\Docs;

use App\Commands\Command;
use App\Services\Documentation\Indexer;
use Illuminate\Contracts\Bus\SelfHandling;

class InitializeElasticSearchIndexes extends Command implements SelfHandling {

	/**
	 * Initializes the indexes for all of our ElasticSearch needs.
	 *
	 * An ElasticSearch index is a name of a container, but also some specific preferences about sharding, etc.
	 *
	 * @param Indexer $indexer
	 */
	public function handle(Indexer $indexer)
	{
		$indexer->initializeIndex();
	}
}
