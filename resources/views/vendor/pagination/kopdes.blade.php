@if ($paginator->hasPages())
    <div style="margin-top:16px; display:flex; gap:6px; align-items:center; flex-wrap:wrap;">
        @if ($paginator->onFirstPage())
            <span class="btn btn-secondary" style="opacity:.5;">&laquo; Sebelumnya</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" class="btn">&laquo; Sebelumnya</a>
        @endif

        @foreach ($elements as $element)
            @if (is_string($element))
                <span style="padding:8px 4px;">{{ $element }}</span>
            @endif
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="btn">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="btn btn-secondary">{{ $page }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach

        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" class="btn">Selanjutnya &raquo;</a>
        @else
            <span class="btn btn-secondary" style="opacity:.5;">Selanjutnya &raquo;</span>
        @endif
    </div>
@endif
