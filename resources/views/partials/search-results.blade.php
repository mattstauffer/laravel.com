<h1>Results for "{{{ $keyword }}}"</h1>
<span id="breaks-TOC-expectations"></span>

@if (count($hits) > 0)
	<ul class="search-results">
		@foreach ($hits as $hit)
			<li><a href="{{ $hit->url() }}/">{{ $hit->title() }}</a><br>
				{!! $hit->fragments() !!}
			</li>
		@endforeach
	</ul>
@else
	<p>No search results found.</p>
@endif
