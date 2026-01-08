@if ($paginator->hasPages())
    <div class="flex items-center justify-between w-full h-16 px-3 border-t border-neutral-200 dark:border-neutral-700">
        {{-- Results Count --}}
        <p class="pl-2 text-sm text-gray-700 dark:text-gray-300">
            {!! __('Showing') !!}
            @if ($paginator->firstItem())
                <span class="font-medium">{{ $paginator->firstItem() }}</span>
                {!! __('to') !!}
                <span class="font-medium">{{ $paginator->lastItem() }}</span>
            @else
                <span class="font-medium">{{ $paginator->count() }}</span>
            @endif
            {!! __('of') !!}
            <span class="font-medium">{{ $paginator->total() }}</span>
            {!! __('results') !!}
        </p>

        {{-- Pagination Navigation --}}
        <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}">
            <ul class="flex items-center text-sm leading-tight bg-white dark:bg-gray-800 border divide-x rounded h-9 text-neutral-500 dark:text-neutral-400 divide-neutral-200 dark:divide-neutral-700 border-neutral-200 dark:border-neutral-700">
                {{-- Previous Page Link --}}
                <li class="h-full">
                    @if ($paginator->onFirstPage())
                        <span class="relative inline-flex items-center h-full px-3 ml-0 rounded-l cursor-not-allowed text-neutral-400 dark:text-neutral-500">
                            <span>{!! __('pagination.previous') !!}</span>
                        </span>
                    @else
                        <button type="button" 
                           wire:click="previousPage"
                           rel="prev"
                           class="relative inline-flex items-center h-full px-3 ml-0 rounded-l group hover:text-neutral-900 dark:hover:text-neutral-100 transition-colors duration-150">
                            <span>{!! __('pagination.previous') !!}</span>
                        </button>
                    @endif
                </li>

                {{-- Pagination Elements --}}
                @foreach ($elements as $element)
                    {{-- "Three Dots" Separator --}}
                    @if (is_string($element))
                        <li class="hidden h-full md:block">
                            <div class="relative inline-flex items-center h-full px-2.5 group">
                                <span>...</span>
                            </div>
                        </li>
                    @endif

                    {{-- Array Of Links --}}
                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <li class="hidden h-full md:block">
                                    <span aria-current="page" class="relative inline-flex items-center h-full px-3 text-neutral-900 dark:text-neutral-100 group bg-gray-50 dark:bg-neutral-700">
                                        <span>{{ $page }}</span>
                                        <span class="box-content absolute bottom-0 left-0 w-full h-px -mx-px translate-y-px border-l border-r bg-neutral-900 dark:bg-neutral-100 border-neutral-900 dark:border-neutral-100"></span>
                                    </span>
                                </li>
                            @else
                                <li class="hidden h-full md:block">
                                    <button type="button"
                                       wire:click="gotoPage({{ $page }})"
                                       aria-label="{{ __('Go to page :page', ['page' => $page]) }}"
                                       class="relative inline-flex items-center h-full px-3 group hover:text-neutral-900 dark:hover:text-neutral-100 transition-colors duration-150">
                                        <span>{{ $page }}</span>
                                        <span class="box-content absolute bottom-0 w-0 h-px -mx-px duration-200 ease-out translate-y-px border-transparent bg-neutral-900 dark:bg-neutral-100 group-hover:border-l group-hover:border-r group-hover:border-neutral-900 dark:group-hover:border-neutral-100 left-1/2 group-hover:left-0 group-hover:w-full"></span>
                                    </button>
                                </li>
                            @endif
                        @endforeach
                    @endif
                @endforeach

                {{-- Next Page Link --}}
                <li class="h-full">
                    @if ($paginator->hasMorePages())
                        <button type="button"
                           wire:click="nextPage"
                           rel="next"
                           class="relative inline-flex items-center h-full px-3 rounded-r group hover:text-neutral-900 dark:hover:text-neutral-100 transition-colors duration-150">
                            <span>{!! __('pagination.next') !!}</span>
                        </button>
                    @else
                        <span class="relative inline-flex items-center h-full px-3 rounded-r cursor-not-allowed text-neutral-400 dark:text-neutral-500">
                            <span>{!! __('pagination.next') !!}</span>
                        </span>
                    @endif
                </li>
            </ul>
        </nav>
    </div>
@endif
