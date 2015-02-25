<?php namespace App\Console\Commands;

use App\Commands\Docs\InitializeElasticSearchIndexes;
use Illuminate\Console\Command;

class InitDocs extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'docs:init';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Initialize all ElasticSearch Documentation Indexes.';

	/**
	 * Execute the console command.
	 *
	 * @return void
	 */
	public function handle(\Illuminate\Contracts\Bus\Dispatcher $bus)
	{
		$bus->dispatch(new InitializeElasticSearchIndexes);
	}
}
