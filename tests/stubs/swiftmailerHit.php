<?php
return [
	'_index' => 'docs.5.0',
	'_type' => 'page',
	'_id' => 'b83a886a5c437ccd9ac15473fd6f1788',
	'_score' => 0.02484156,
	'_source' => [
		'slug' => 'mail',
		'title' => 'Mail',
		'body.md' => file_get_contents(__DIR__ . '/mail.md'),
		'body.highlighting' => file_get_contents(__DIR__ . '/mailBodyHighlighting.txt'),
		'body.html' => file_get_contents(__DIR__ . '/mail.html')
	],
	'highlight' => [
		'body.highlighting' => [
			file_get_contents(__DIR__ . '/swiftmailerHighlightedBodyHighlighting.txt')
		]
	]
];
