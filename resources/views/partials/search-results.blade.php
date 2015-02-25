<h1>Results for "{{{ $keyword }}}"</h1>
<style>
	.hack-bro {
		list-style-type: none;
	}
	.hack-bro:before {
		content: none;
	}
</style>

@if (count($hits) > 0)
	<ul>
		@foreach ($hits as $hit)
			<li><a href="{{ url('docs/' . $hit['_source']['slug']) }}/">{{ $hit['_source']['title'] }}</a><br>
				<ul>
				@foreach ($hit['highlight']['body.plain'] as $fragment)
					<li class="hack-bro">...{!! $fragment !!}...</li>
				@endforeach
				</ul>
			</li>
		@endforeach
	</ul>
@else
	<p>No search results found.</p>
@endif
