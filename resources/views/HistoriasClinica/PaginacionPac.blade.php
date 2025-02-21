@if ($paginator->hasPages())
    <ul class="pagination pageConsulta">
        {{-- Previous Page Link --}}
        @if ($paginator->onFirstPage())
            <li class="paginate_button page-item previous disabled" id="example1_previous">
                <a href="#" aria-controls="example1" data-dt-idx="0" tabindex="0" class="page-link">Anterior</a>
            </li>
        @else
            <li class="paginate_button page-item previous" id="example1_previous">
                <a href="{{ $paginator->previousPageUrl() }}" aria-controls="example1" data-dt-idx="0" tabindex="0" class="page-link" rel="prev">Anterior</a>
            </li>
        @endif

        {{-- Pagination Elements --}}
        @foreach ($elements as $element)
            {{-- "Three Dots" Separator --}}
            @if (is_string($element))
                <li class="paginate_button page-item disabled">
                    <span class="page-link">{{ $element }}</span>
                </li>
            @endif

            {{-- Array Of Links --}}
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <li class="paginate_button page-item active">
                            <a href="#" aria-controls="example1" data-dt-idx="{{ $page }}" tabindex="0" class="page-link">{{ $page }}</a>
                        </li>
                    @else
                        <li class="paginate_button page-item">
                            <a href="{{ $url }}" aria-controls="example1" data-dt-idx="{{ $page }}" tabindex="0" class="page-link">{{ $page }}</a>
                        </li>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next Page Link --}}
        @if ($paginator->hasMorePages())
            <li class="paginate_button page-item next" id="example1_next">
                <a href="{{ $paginator->nextPageUrl() }}" aria-controls="example1" data-dt-idx="{{ $paginator->currentPage() + 1 }}" tabindex="0" class="page-link" rel="next">Siguiente</a>
            </li>
        @else
            <li class="paginate_button page-item next disabled" id="example1_next">
                <a href="#" aria-controls="example1" data-dt-idx="{{ $paginator->currentPage() + 1 }}" tabindex="0" class="page-link">Siguiente</a>
            </li>
        @endif
    </ul>
@endif
