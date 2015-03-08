<?php namespace App\Http\Controllers;

use App;
use App\Documentation;
use Exception;
use Illuminate\Support\Facades\Log;
use Input;
use Redirect;

class DocsSearchController extends Controller {

	/**
	 * The documentation repository.
	 *
	 * @var Documentation
	 */
	protected $docs;

	/**
	 * Create a new controller instance.
	 *
	 * @param  Documentation  $docs
	 * @return void
	 */
	public function __construct(Documentation $docs)
	{
		$this->docs = $docs;
	}

	/**
	 * Show search results
	 * 
	 * @return Response
	 */
	public function search($version)
	{
		if (! $keyword = Input::get('keyword'))
		{
			return Redirect::to('docs/' . $version);
		}

		/** @var App\Services\Documentation\Searcher $client */
		$client = App::make('App\Services\Documentation\Searcher');

		try {
			$hits = $client->searchForTerm($version, $keyword);
		} catch (Exception $e) {
			Log::error($e);

			return Redirect::to('docs/' . $version);
		}

		$content = view('partials.search-results', [
			'hits' => $hits,
			'keyword' => $keyword,
		]);

		return view('docs', [
			'index' => $this->docs->getIndex($version),
			'content' => $content,
			'currentVersion' => $version,
			'versions' => $this->getDocVersions(),
			'keyword' => $keyword,
		]);
	}

	/**
	 * Get the available documentation versions.
	 *
	 * @return array
	 */
	protected function getDocVersions()
	{
		return Documentation::getDocVersions();
	}

}
