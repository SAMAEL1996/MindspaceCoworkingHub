@props([
    'paginator' => null,
    'pageName' => 'page',
    'livewire' => false,
])

@if($paginator)
    @php
        $window = \Illuminate\Pagination\UrlWindow::make($paginator);

        $elements = array_filter([
            $window['first'],
            is_array($window['slider']) ? '...' : null,
            $window['slider'],
            is_array($window['last']) ? '...' : null,
            $window['last'],
        ]);
    @endphp
@endif

@if($paginator && $paginator->hasPages())
    <nav {{ $attributes->class('d-flex justify-content-center') }} aria-label="pagination">
        <ul class="pagination mb-0">
            <li class="page-item {{ $paginator->onFirstPage() ? 'disabled' : '' }}">
                @if($paginator->onFirstPage())
                    <span class="page-link" aria-label="Previous" aria-disabled="true">
                        <span aria-hidden="true"><i class="uil uil-arrow-left"></i></span>
                    </span>
                @elseif($livewire)
                    <a
                        class="page-link"
                        href="#"
                        aria-label="Previous"
                        rel="prev"
                        wire:click.prevent="previousPage('{{ $pageName }}')"
                        wire:loading.attr="disabled"
                    >
                        <span aria-hidden="true"><i class="uil uil-arrow-left"></i></span>
                    </a>
                @else
                    <a class="page-link" href="{{ $paginator->previousPageUrl() }}" aria-label="Previous" rel="prev">
                        <span aria-hidden="true"><i class="uil uil-arrow-left"></i></span>
                    </a>
                @endif
            </li>

            @foreach($elements as $element)
                @if(is_string($element))
                    <li class="page-item disabled" aria-disabled="true">
                        <span class="page-link">{{ $element }}</span>
                    </li>
                @endif

                @if(is_array($element))
                    @foreach($element as $page => $url)
                        <li class="page-item {{ $page == $paginator->currentPage() ? 'active' : '' }}" wire:key="pagination-{{ $pageName }}-{{ $page }}">
                            @if($page == $paginator->currentPage())
                                <span class="page-link" aria-current="page">{{ $page }}</span>
                            @elseif($livewire)
                                <a
                                    class="page-link"
                                    href="#"
                                    wire:click.prevent="gotoPage({{ $page }}, '{{ $pageName }}')"
                                    wire:loading.attr="disabled"
                                >
                                    {{ $page }}
                                </a>
                            @else
                                <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                            @endif
                        </li>
                    @endforeach
                @endif
            @endforeach

            <li class="page-item {{ $paginator->hasMorePages() ? '' : 'disabled' }}">
                @if($paginator->hasMorePages())
                    @if($livewire)
                        <a
                            class="page-link"
                            href="#"
                            aria-label="Next"
                            rel="next"
                            wire:click.prevent="nextPage('{{ $pageName }}')"
                            wire:loading.attr="disabled"
                        >
                            <span aria-hidden="true"><i class="uil uil-arrow-right"></i></span>
                        </a>
                    @else
                        <a class="page-link" href="{{ $paginator->nextPageUrl() }}" aria-label="Next" rel="next">
                            <span aria-hidden="true"><i class="uil uil-arrow-right"></i></span>
                        </a>
                    @endif
                @else
                    <span class="page-link" aria-label="Next" aria-disabled="true">
                        <span aria-hidden="true"><i class="uil uil-arrow-right"></i></span>
                    </span>
                @endif
            </li>
        </ul>
    </nav>
@endif
