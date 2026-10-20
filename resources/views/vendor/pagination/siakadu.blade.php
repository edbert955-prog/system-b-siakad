@if ($paginator->hasPages())
    <nav class="siakadu-pagination" role="navigation" aria-label="Pagination">
        <div class="siakadu-page-actions">
            @if ($paginator->onFirstPage())
                <span class="page-link disabled" aria-disabled="true">‹ Sebelumnya</span>
            @else
                <a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev">‹ Sebelumnya</a>
            @endif

            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="page-link disabled" aria-disabled="true">{{ $element }}</span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="page-link active" aria-current="page">{{ $page }}</span>
                        @else
                            <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next">Berikutnya ›</a>
            @else
                <span class="page-link disabled" aria-disabled="true">Berikutnya ›</span>
            @endif
        </div>
    </nav>
@endif
