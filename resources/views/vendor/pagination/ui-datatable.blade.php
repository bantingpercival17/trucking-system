@if ($paginator->hasPages())
    <div class="dataTables_paginate paging_simple_numbers">
        <ul class="pagination mb-0">

            {{-- Previous --}}
            @if ($paginator->onFirstPage())
                <li class="paginate_button page-item previous disabled">
                    <span class="page-link">Previous</span>
                </li>
            @else
                <li class="paginate_button page-item previous">
                    <a class="page-link"
                       href="{{ $paginator->previousPageUrl() }}"
                       rel="prev">Previous</a>
                </li>
            @endif

            {{-- Page Numbers --}}
            @foreach ($elements as $element)

                {{-- "Three Dots" --}}
                @if (is_string($element))
                    <li class="paginate_button page-item disabled">
                        <span class="page-link">{{ $element }}</span>
                    </li>
                @endif

                {{-- Array of links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="paginate_button page-item active">
                                <span class="page-link">{{ $page }}</span>
                            </li>
                        @else
                            <li class="paginate_button page-item">
                                <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                            </li>
                        @endif
                    @endforeach
                @endif

            @endforeach

            {{-- Next --}}
            @if ($paginator->hasMorePages())
                <li class="paginate_button page-item next">
                    <a class="page-link"
                       href="{{ $paginator->nextPageUrl() }}"
                       rel="next">Next</a>
                </li>
            @else
                <li class="paginate_button page-item next disabled">
                    <span class="page-link">Next</span>
                </li>
            @endif

        </ul>
    </div>
@endif