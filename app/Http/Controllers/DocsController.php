<?php namespace App\Http\Controllers;

use App;
use App\Documentation;
use Exception;
use Illuminate\Support\Facades\Log;
use Input;
use Redirect;
use Symfony\Component\DomCrawler\Crawler;

class DocsController extends Controller {

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
	 * Show the root documentation page (/docs).
	 *
	 * @return Response
	 */
	public function showRootPage()
	{
		return redirect('docs/'.DEFAULT_VERSION);
	}

	/**
	 * Show a documentation page.
	 *
	 * @return Response
	 */
	public function show($version, $page = null)
	{
		if ( ! $this->isVersion($version)) {
			return redirect('docs/'.DEFAULT_VERSION.'/'.$version, 301);
		}

		$content = $this->docs->get($version, $page ?: 'installation');

		$title = (new Crawler($content))->filterXPath('//h1');

		if (is_null($content)) {
			abort(404);
		}

		return view('docs', [
			'title' => count($title) ? $title->text() : null,
			'index' => $this->docs->getIndex($version),
			'content' => $content,
			'currentVersion' => $version,
			'versions' => $this->getDocVersions(),
		]);
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
	 * Determine if the given URL segment is a valid version.
	 *
	 * @param  string  $version
	 * @return bool
	 */
	protected function isVersion($version)
	{
		return in_array($version, array_keys($this->getDocVersions()));
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
