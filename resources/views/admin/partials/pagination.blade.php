@if ($paginator->hasPages())
    <nav class="admin-pagination" role="navigation" aria-label="Pagination">
        @foreach ($elements as $element)
            @if (is_string($element))
                <span class="admin-pagination-ellipsis">{{ $element }}</span>
            @endif

            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page === $paginator->currentPage())
                        <span class="admin-pagination-link is-active" aria-current="page">{{ $page }}</span>
                    @else
                        <a class="admin-pagination-link" href="{{ $url }}">{{ $page }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach
    </nav>
@endif
