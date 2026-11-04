@if ($paginator->hasPages())
    <div class="flex gap-1.5 items-center flex-wrap print:hidden">
        @if ($paginator->onFirstPage())
            <span class="inline-block px-3.5 py-2 bg-stone-500 text-white text-sm rounded-md opacity-50">&laquo;
                Sebelumnya</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}"
                class="inline-block px-3.5 py-2 bg-red-700 hover:bg-red-800 text-white text-sm font-bold rounded-md transition">&laquo;
                Sebelumnya</a>
        @endif

        @foreach ($elements as $element)
            @if (is_string($element))
                <span class="px-2 py-2 text-sm">{{ $element }}</span>
            @endif
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span
                            class="inline-block px-3.5 py-2 bg-red-700 text-white text-sm font-bold rounded-md">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}"
                            class="inline-block px-3.5 py-2 bg-stone-500 hover:bg-stone-600 text-white text-sm rounded-md transition">{{ $page }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach

        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}"
                class="inline-block px-3.5 py-2 bg-red-700 hover:bg-red-800 text-white text-sm font-bold rounded-md transition">Selanjutnya
                &raquo;</a>
        @else
            <span class="inline-block px-3.5 py-2 bg-stone-500 text-white text-sm rounded-md opacity-50">Selanjutnya
                &raquo;</span>
        @endif
    </div>
@endif
