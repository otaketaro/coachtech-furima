@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/items.css') }}?v={{ time() }}">
<style>
    /* ==== 一覧ページで発生する謎オーバーレイ対策（必要なら残す） ==== */
    html::before,
    html::after,
    body::before,
    body::after {
        content: none !important;
        background: none !important;
        background-image: none !important;
        box-shadow: none !important;
    }

    main.items *::before,
    main.items *::after,
    main.items li::marker {
        content: none !important;
        background: none !important;
        background-image: none !important;
    }

    main.items ul,
    main.items li {
        list-style: none !important;
    }

    main.items {
        background-color: #fff !important;
    }
</style>
@endsection

@section('content')
@php
$tab = request('tab'); // 'mylist' or null
$q = request('q');
@endphp

<main class="items">
    {{-- タブ（おすすめ / マイリスト） --}}
    <div class="items__tabs" role="tablist" aria-label="商品一覧タブ">
        <a role="tab"
            aria-selected="{{ $tab !== 'mylist' ? 'true' : 'false' }}"
            class="items__tab {{ $tab !== 'mylist' ? 'is-active is-recommend' : '' }}"
            href="{{ route('items.index', array_filter(['q' => $q])) }}">
            おすすめ
        </a>

        <a role="tab"
            aria-selected="{{ $tab === 'mylist' ? 'true' : 'false' }}"
            class="items__tab {{ $tab === 'mylist' ? 'is-active is-mylist' : '' }}"
            href="{{ route('items.index', array_filter(['tab' => 'mylist', 'q' => $q])) }}">
            マイリスト
        </a>
    </div>
    <div class="items__underline" aria-hidden="true"></div>

    {{-- === ここから両対応化ブロック（get()/paginate()のどちらでもOK） === --}}
    @php
    use Illuminate\Contracts\Pagination\Paginator as PaginatorContract;

    $isPaginator = $items instanceof PaginatorContract;
    $count = $isPaginator
    ? $items->count()
    : (($items instanceof \Illuminate\Support\Collection) ? $items->count() : 0);
    @endphp

    @if($count > 0)
    <ul class="items__grid">
        @foreach($items as $item)
        <li class="item-card">
            <a class="item-card__link" href="{{ route('items.show', $item) }}">
                <div class="item-card__image-wrap">
                    @php
                    $p = $item->image_path;
                    $isUrl = is_string($p) && preg_match('/^https?:\/\//i', $p);
                    $src = $isUrl ? $p : asset('storage/'.$p);
                    @endphp
                    <img class="item-card__image"
                        src="{{ $src }}"
                        alt="{{ $item->title }}"
                        width="290" height="290"
                        loading="lazy"
                        onerror="this.onerror=null; this.src='{{ asset('images/placeholder-290.png') }}';">

                    @if($item->is_sold)
                    <span class="item-card__badge">Sold</span>
                    @endif
                </div>
                <div class="item-card__body">
                    <p class="item-card__title">{{ $item->title }}</p>
                </div>
            </a>
        </li>
        @endforeach
    </ul>

    @if($isPaginator && $items->hasPages())
    <div class="items__pagination">
        {{ $items->withQueryString()->onEachSide(1)->links('pagination::simple-tailwind') }}
    </div>
    @endif
    @else
    <p class="items__empty">商品がありません。</p>
    @endif
    {{-- === 両対応化ここまで === --}}
</main>
@endsection