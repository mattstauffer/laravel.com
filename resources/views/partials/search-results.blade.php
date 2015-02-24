<h1>Results for "{{{ $keyword }}}"</h1>

@if (count($hits) > 0)
    <ul>
        @foreach ($hits as $hit)
            <li><a href="{{ url('docs/' . $hit['_source']['slug']) }}/">{{ $hit['_source']['title'] }}</a>
            </li>
        @endforeach
    </ul>
@else
    <p>No search results found.</p>
@endif
