@if ($paginator->hasPages())
    <div class="flex items-center justify-between px-6 py-4 bg-slate-50/50 border-t border-slate-100 rounded-b-3xl">
        <div class="flex-1 flex items-center justify-between sm:hidden">
            @if ($paginator->onFirstPage())
                <span
                    class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-slate-400 bg-white border border-slate-200 cursor-default rounded-md">
                    {!! __('pagination.previous') !!}
                </span>
            @else
                <button wire:click="previousPage" wire:loading.attr="disabled"
                    class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 leading-5 rounded-md hover:text-slate-500 focus:outline-none focus:ring ring-slate-300 focus:border-slate-300 active:bg-slate-100 active:text-slate-700 transition ease-in-out duration-150">
                    {!! __('pagination.previous') !!}
                </button>
            @endif

            @if ($paginator->hasMorePages())
                <button wire:click="nextPage" wire:loading.attr="disabled"
                    class="relative inline-flex items-center px-4 py-2 ml-3 text-sm font-medium text-slate-700 bg-white border border-slate-300 leading-5 rounded-md hover:text-slate-500 focus:outline-none focus:ring ring-slate-300 focus:border-slate-300 active:bg-slate-100 active:text-slate-700 transition ease-in-out duration-150">
                    {!! __('pagination.next') !!}
                </button>
            @else
                <span
                    class="relative inline-flex items-center px-4 py-2 ml-3 text-sm font-medium text-slate-400 bg-white border border-slate-200 cursor-default rounded-md">
                    {!! __('pagination.next') !!}
                </span>
            @endif
        </div>

        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
            <div>
                <p class="text-[12px] text-slate-500 font-medium">
                    Menampilkan <span class="font-bold text-slate-900">{{ $paginator->firstItem() }}</span> sampai <span
                        class="font-bold text-slate-900">{{ $paginator->lastItem() }}</span> dari <span
                        class="font-bold text-slate-900">{{ $paginator->total() }}</span> data
                </p>
            </div>

            <div>
                <nav class="relative z-0 inline-flex rounded-xl shadow-sm -space-x-px" aria-label="Pagination">
                    {{-- Previous Page Link --}}
                    @if ($paginator->onFirstPage())
                        <span aria-disabled="true" aria-label="{{ __('pagination.previous') }}">
                            <span
                                class="relative inline-flex items-center px-3 py-2 text-slate-300 bg-white border border-slate-100 rounded-l-xl cursor-default"
                                aria-hidden="true">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
                                        clip-rule="evenodd" />
                                </svg>
                            </span>
                        </span>
                    @else
                        <button wire:click="previousPage" rel="prev"
                            class="relative inline-flex items-center px-3 py-2 text-slate-500 bg-white border border-slate-100 rounded-l-xl hover:bg-slate-50 focus:z-10 focus:outline-none focus:ring-4 focus:ring-green-900/5 transition-all"
                            aria-label="{{ __('pagination.previous') }}">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
                                    clip-rule="evenodd" />
                            </svg>
                        </button>
                    @endif

                    {{-- Pagination Elements --}}
                    @foreach ($elements as $element)
                        {{-- "Three Dots" Separator --}}
                        @if (is_string($element))
                            <span aria-disabled="true">
                                <span
                                    class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-slate-400 bg-white border border-slate-100 cursor-default">{{ $element }}</span>
                            </span>
                        @endif

                        {{-- Array Of Links --}}
                        @if (is_array($element))
                            @foreach ($element as $page => $url)
                                @if ($page == $paginator->currentPage())
                                    <span aria-current="page">
                                        <span
                                            class="relative inline-flex items-center px-4 py-2 text-[13px] font-extrabold text-green-900 bg-green-50 border border-green-100/50 z-10">{{ $page }}</span>
                                    </span>
                                @else
                                    <button wire:click="gotoPage({{ $page }})"
                                        class="relative inline-flex items-center px-4 py-2 text-[13px] font-semibold text-slate-500 bg-white border border-slate-100 hover:bg-slate-50 focus:z-10 focus:outline-none focus:ring-4 focus:ring-green-900/5 transition-all">{{ $page }}</button>
                                @endif
                            @endforeach
                        @endif
                    @endforeach

                    {{-- Next Page Link --}}
                    @if ($paginator->hasMorePages())
                        <button wire:click="nextPage" rel="next"
                            class="relative inline-flex items-center px-3 py-2 text-slate-500 bg-white border border-slate-100 rounded-r-xl hover:bg-slate-50 focus:z-10 focus:outline-none focus:ring-4 focus:ring-green-900/5 transition-all"
                            aria-label="{{ __('pagination.next') }}">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                    clip-rule="evenodd" />
                            </svg>
                        </button>
                    @else
                        <span aria-disabled="true" aria-label="{{ __('pagination.next') }}">
                            <span
                                class="relative inline-flex items-center px-3 py-2 text-slate-300 bg-white border border-slate-100 rounded-r-xl cursor-default"
                                aria-hidden="true">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                        clip-rule="evenodd" />
                                </svg>
                            </span>
                        </span>
                    @endif
                </nav>
            </div>
        </div>
    </div>
@endif
