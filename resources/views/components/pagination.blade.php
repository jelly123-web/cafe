@if ($paginator->hasPages())
    <nav class="pagination-wrap" aria-label="Pagination">
        <div class="pagination-meta">
            Menampilkan {{ $paginator->firstItem() ?? 0 }}-{{ $paginator->lastItem() ?? 0 }}
            dari {{ $paginator->total() }} data
        </div>

        <div class="pagination-links">
            @if ($paginator->onFirstPage())
                <span class="pagination-link disabled">Sebelumnya</span>
            @else
                <a class="pagination-link" href="{{ $paginator->previousPageUrl() }}" rel="prev">Sebelumnya</a>
            @endif

            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="pagination-dots">{{ $element }}</span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="pagination-link active">{{ $page }}</span>
                        @else
                            <a class="pagination-link" href="{{ $url }}">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <a class="pagination-link" href="{{ $paginator->nextPageUrl() }}" rel="next">Berikutnya</a>
            @else
                <span class="pagination-link disabled">Berikutnya</span>
            @endif
        </div>
    </nav>
@endif
