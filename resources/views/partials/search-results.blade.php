<h1>Results for "{{{ $keyword }}}"</h1>
<style>
	.search-results {
		list-style-type: none;
		padding-left: 0;
	}

		.search-results li {
			border-bottom: 1px solid #eee;
			margin-bottom: 1em;
			padding-bottom: 1em;
		}

		.search-results li a {
			font-size: 1.25em;
			font-weight: bold;
			text-decoration: none;
		}

	.search-ellipses {
		color: #bbb;
		margin-left: 0.25em;
		margin-right: 0.25em;
	}
	.search-line-break {
		color: #eee;
		margin-left: 0.35em;
		margin-right: 0.35em;
	}
</style>
<span id="breaks-TOC-expectations"></span>

@if (count($hits) > 0)
	<ul class="search-results">
		@foreach ($hits as $hit)
			<li><a href="{{ url('docs/' . $hit['_source']['slug']) }}/">{{ $hit['_source']['title'] }}</a><br>
				@if (array_key_exists('highlight', $hit))
					@foreach ($hit['highlight']['body.highlighting'] as $fragment)
						{{-- @todo fix the ... to only apply when appropriate. Also fix this hacky display crap. --}}
						<span class="search-ellipses">...</span>{!! str_replace("\n", "<span class='search-line-break'>|</span>", trim(str_replace("\n\n", "\n", str_replace("\n\n", "\n", $fragment)))) !!}
					@endforeach
					<span class="search-ellipses">...</span>
				@endif
			</li>
		@endforeach
	</ul>
@else
	<p>No search results found.</p>
@endif
