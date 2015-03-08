<?php  namespace App\Services\Documentation; 

class Hit
{
	private $id;
	private $score;
	private $highlight;

	private $title;
	private $slug;
	private $bodyMarkdown;
	private $bodyHighlighting;
	private $bodyHtml;

	public $ellipsis = '<span class="search-ellipsis">...</span>';

	public function __construct(array $hit)
	{
		$this->id = $hit['_id'];
		$this->score = $hit['_score'];
		$this->highlight = array_key_exists('highlight', $hit) ? $hit['highlight'] : array();

		$source = $hit['_source'];

		$this->title = $source['title'];
		$this->slug = $source['slug'];
		$this->bodyMarkdown = $source['body.md'];
		$this->bodyHighlighting = $source['body.highlighting'];
		$this->bodyHtml = $source['body.html'];
	}

	public function title()
	{
		return $this->title;
	}

	public function url()
	{
		return url('docs/' . $this->slug);
	}

	public function fragments()
	{
		$return = '';

		if (! $this->hasHighlight())
		{
			return $return;
		}

		foreach ($this->highlight['body.highlighting'] as $i => $fragment)
		{
			if ($i !== 0 || ! $this->fragmentAtBeginningOfBody($fragment)) {
				$return .= $this->ellipsis;
			}

			$return .= $this->formatFragment($fragment);
		}

		if (! $this->fragmentAtEndOfBody(end($this->highlight['body.highlighting'])))
		{
			$return = $return . $this->ellipsis;
		}

		return $return;
	}

	/**
	 * Return whether this hit returned one or more highlighted fragments
	 *
	 * @return bool
	 */
	public function hasHighlight()
	{
		return ! empty($this->highlight);
	}

	/**
	 * Format a fragment to be displayed in a search listing
	 *
	 * Converts longer line breaks to a single line break and replaces with a stylized |;
	 * prepends ellipsis if appropriate
	 *
	 * @param $fragment
	 * @return mixed|string
	 */
	private function formatFragment($fragment)
	{
		$fragment = trim(str_replace(
			["\n\n\n", "\n\n"],
			["\n", "\n"],
			$fragment
		));

		$fragment = str_replace(
			"\n",
			"<span class='search-line-break'>|</span>",
			$fragment
		);

		return $fragment;
	}

	/**
	 * Return whether the fragment is the beginning of its respective body
	 *
	 * Compares the fragment to the beginning of the body to see if they're the same.
	 *
	 * @param $fragment
	 * @return bool
	 */
	private function fragmentAtBeginningOfBody($fragment)
	{
		$pos = strpos(
			trim(strip_tags($this->bodyHighlighting)),
			trim(strip_tags($fragment))
		);

		return $pos === 0;
	}

	/**
	 * Return whether the fragment is the end of its respective body
	 *
	 * Compares the fragment to the end of the body to see if they're the same.
	 *
	 * @param $fragment
	 * @return bool
	 */
	private function fragmentAtEndOfBody($fragment)
	{
		$fragment = trim(strip_tags($fragment));
		$body = trim(strip_tags($this->bodyHighlighting));

		$pos = strpos($body, $fragment);

		return ((strlen($body) - strlen($fragment)) == $pos);
	}
}
