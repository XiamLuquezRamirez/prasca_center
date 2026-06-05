@if ($paginator->hasPages())
    <ul class="pagination" id="paginationAutorizaciones">
        @if ($paginator->onFirstPage())
            <li class="paginate_button page-item previous disabled">
                <a href="#" class="page-link">Anterior</a>
            </li>
        @else
            <li class="paginate_button page-item previous">
                <a href="{{ $paginator->previousPageUrl() }}" class="page-link" rel="prev">Anterior</a>
            </li>
        @endif

        @foreach ($elements as $element)
            @if (is_string($element))
                <li class="paginate_button page-item disabled">
                    <span class="page-link">{{ $element }}</span>
                </li>
            @endif
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <li class="paginate_button page-item active">
                            <a href="#" class="page-link">{{ $page }}</a>
                        </li>
                    @else
                        <li class="paginate_button page-item">
                            <a href="{{ $url }}" class="page-link">{{ $page }}</a>
                        </li>
                    @endif
                @endforeach
            @endif
        @endforeach

        @if ($paginator->hasMorePages())
            <li class="paginate_button page-item next">
                <a href="{{ $paginator->nextPageUrl() }}" class="page-link" rel="next">Siguiente</a>
            </li>
        @else
            <li class="paginate_button page-item next disabled">
                <a href="#" class="page-link">Siguiente</a>
            </li>
        @endif
    </ul>
@endif
