<?php namespace App\Services\Documentation;

use Elasticsearch\Client;
use Elasticsearch\Common\Exceptions\Missing404Exception;
use Illuminate\Support\Collection;

class Searcher
{
	/**
	 * @var Client
	 */
	private $client;

	public function __construct(Client $client)
	{
		$this->client = $client;
	}

	/**
	 * @param  string $version
	 * @param  string $term
	 * @return array
	 * @throws \Exception
	 */
	public function searchForTerm($version, $term)
	{
		$params['index'] = $this->getIndexName($version);
		$params['type'] = 'page';


		$params['body']['query']['multi_match']['query'] = $term;
		$params['body']['query']['multi_match']['fields'] = [
			"title^3", // Boost title's importance by 3
			"body.md"
		];
		$params['body']['highlight']['pre_tags'] = ["<mark>"];
		$params['body']['highlight']['post_tags'] = ["</mark>"];
		$params['body']['highlight']['fields']['body.highlighting'] = [
			"number_of_fragments" => 2,
			"fragment_size" => 120
		];
			
		try {
			$response = $this->client->search($params);
		} catch (Missing404Exception $e) {
			throw new \Exception('ElasticSearch Index was not initialized.');
		}

		return $this->transformHits($response['hits']['hits']);
	}

	/**
	 * @param array $hits
	 * @return Collection
	 */
	private function transformHits($hits)
	{
		return new Collection(array_map(function($hit) {
			return new Hit($hit);
		}, $hits));
	}

	/**
	 * Get the ElasticSearch index name for this version
	 *
	 * @todo Duplicated to Indexer; fix
	 * @param $version
	 * @return string
	 */
	private function getIndexName($version)
	{
		return 'docs.' . $version;
	}
}
