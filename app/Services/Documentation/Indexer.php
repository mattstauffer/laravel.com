<?php namespace App\Services\Documentation;

use App\Documentation;
use Elasticsearch\Client;
use Elasticsearch\Common\Exceptions\BadRequest400Exception;
use Exception;
use Illuminate\Filesystem\Filesystem;
use ParsedownExtra;

class Indexer {

	/**
	 * @var Client
	 */
	private $client;

	/**
	 * @var ParsedownExtra
	 */
	private $markdown;

	/**
	 * @var Filesystem
	 */
	private $files;

	private $noIndex = [
		'documentation',
		'license'
	];

	public function __construct(Client $client, ParsedownExtra $markdown, Filesystem $files)
	{
		$this->client = $client;
		$this->markdown = $markdown;
		$this->files = $files;
	}

	/**
	 * Initialize ElasticSearch Indexes
	 *
	 * ElasticSearch will automatically create an index if it's added to, even
	 * if it doesn't exist. However, if you need to customize particular
	 * parameters for your indexes--for example, how it shards--then you can
	 * initialize the indexes before you use them.
	 *
	 * Since this method isn't doing anything custom at the moment, it's not
	 * actually necessary until it's updated to pass custom parameters to the
	 * create() method.
	 */
	public function initializeIndexes()
	{
		foreach (Documentation::getDocVersions() as $versionKey => $versionTitle)
		{
			try {
				$this->client->indices()->create([
					'index' => $this->getIndexName($versionKey),
				]);
			} catch (BadRequest400Exception $e) {
				// Cool, we already have that index. Still want to create the others...
			}
		}
	}

	/**
	 * Index all docs for all versions from local markdown files
	 */
	public function indexAllDocuments()
	{
		foreach (Documentation::getDocVersions() as $versionKey => $versionTitle)
		{
			$this->indexAllDocumentsForVersion($versionKey);
		}
	}

	/**
	 * Index all docs for this version
	 *
	 * @param $version
	 */
	public function indexAllDocumentsForVersion($version)
	{
		foreach ($this->files->files($this->getDocsPath($version)) as $path)
		{
			if (! in_array($this->getSlugFromPath($path), $this->noIndex))
			{
				$this->indexDocument($version, $path);
			}
		}
	}

	/**
	 * Index a given document in ElasticSearch
	 *
	 * @param string $version
	 * @param string $path
	 */
	public function indexDocument($version, $path)
	{
		$slug = $this->getSlugFromPath($path);
		$markdown = $this->getFileContents($path);
		$title = $this->extractTitleFromMarkdown($markdown);
		$html = $this->convertMarkdownToHtml($markdown);
		$highlighting = $this->convertMarkdownForHighlighting($markdown);

		$params['index'] = $this->getIndexName($version);
		$params['body'] = [
			'slug' => $slug,
			'title' => $title,
			'body.md' => $markdown,
			'body.html' => $html,
			'body.highlighting' => $highlighting
		];
		$params['type'] = 'page';
		$params['id'] = $this->generateDocIdFromSlug($slug);

		$return = $this->client->index($params);

		echo "Indexed $version.$slug" . PHP_EOL;
	}

	/**
	 * Convert a Markdown file to an HTML-less version, ready for being highlighted in search results
	 *
	 * @param $markdown
	 * @return string
	 */
	private function convertMarkdownForHighlighting($markdown)
	{
		// In the current structure of the documentation, it starts with a title, then table of contents,
		// and then the first real content is the first section's marker: e.g. <a name="introduction"></a>
		// So, the hacky way of stripping out the title and the TOC is to just delete everything
		// up to the first instance of <a

		// Strip title and TOC
		$content = substr($markdown, strpos($markdown, '<a'));

		// Drop code blocks
		$joined = array_map(function($line) {
			if (strpos($line, "\t") !== 0) {
				return $line;
			}
		}, explode("\n", $content));

		$content = implode("\n", array_filter($joined));

		// Convert to HTML and then strip tags
		$content = $this->convertMarkdownToHtml($content);
		$content = strip_tags($content);

		// Catch anything we missed on strip_tags
		$content = str_replace([
			'&quot;'
		], [
			'"'
		], $content);

		return $content;
	}

	/**
	 * Get the ElasticSearch index name for this version
	 *
	 * @todo Duplicated to Searcher; fix
	 * @param $version
	 * @return string
	 */
	private function getIndexName($version)
	{
		return 'docs.' . $version;
	}

	/**
	 * @param string $version
	 * @return string
	 */
	private function getDocsPath($version)
	{
		return base_path('resources/docs/' . $version . '/');
	}

	/**
	 * @param  string $slug
	 * @return string
	 */
	private function generateDocIdFromSlug($slug)
	{
		return md5($slug);
	}

	/**
	 * @param  string $path
	 * @return string
	 * @throws Exception
	 */
	private function getFileContents($path)
	{
		$this->verifyFileExists($path);

		return file_get_contents($path);
	}

	/**
	 * @param  string $path
	 * @throws Exception
	 */
	private function verifyFileExists($path)
	{
		if (! file_exists($path))
		{
			throw new Exception('File does not exist at path: ' . $path);
		}
	}

	/**
	 * @param  string $markdown
	 * @return string
	 */
	private function convertMarkdownToHtml($markdown)
	{
		return $this->markdown->text($markdown);
	}

	/**
	 * @param  string $markdown
	 * @return string
	 */
	private function extractTitleFromMarkdown($markdown)
	{
		preg_match_all("/^# (.*)$/m", $markdown, $m);

		return $m[1][0];
	}

	/**
	 * @param  string $path
	 * @return string
	 */
	public function getSlugFromPath($path)
	{
		$fileName = last(explode('/', $path));

		return str_replace('.md', '', $fileName);
	}
}
