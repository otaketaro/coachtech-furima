@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/items.css') }}?v={{ time() }}">
<link rel="stylesheet" href="{{ asset('css/mypage.css') }}?v={{ time() }}">
@endsection

@section('content')
@php
use Illuminate\Support\Facades\Storage;

$tab = $tab ?? request('page', 'sell');

$img = function ($path) {
if (empty($path)) return asset('images/placeholder-290.png');
if (is_string($path) && preg_match('/^https?:\/\//i', $path)) return $path;
return Storage::url($path);
};
@endphp

<div class="pg-mypage">

    {{-- ユーザー情報ブロック --}}
    <section class="user-info">
        <div class="user-info-inner">
            @if (!empty($user->avatar_path))
            <img class="user-avatar" src="{{ Storage::url($user->avatar_path) }}" alt="プロフィール画像">
            @else
            <div class="user-avatar" aria-hidden="true"></div>
            @endif

            <div class="user-meta">
                <div class="user-name">{{ $user->name }}</div>
            </div>

            <div class="user-actions">
                <a href="{{ route('mypage.profile.edit') }}" class="btn-edit">プロフィールを編集</a>
            </div>
        </div>
    </section>

    {{-- タブ --}}
    <nav class="mypage-tabs">
        <a href="{{ route('mypage.index', ['page' => 'sell']) }}"
            class="tab-link {{ $tab === 'sell' ? 'is-active' : '' }}">出品した商品</a>
        <a href="{{ route('mypage.index', ['page' => 'buy'])  }}"
            class="tab-link {{ $tab === 'buy'  ? 'is-active' : '' }}">購入した商品</a>
    </nav>
    <div class="tabs-divider" aria-hidden="true"></div>

    {{-- 一覧と同じカード構造＋Soldバッジ --}}
    <main class="items" style="background:none">
        <ul class="items__grid">
            {{-- 出品した商品 --}}
            @if ($tab === 'sell' && isset($items))
            @forelse ($items as $item)
            @php
            $src = $img($item->image_path ?? null);
            $isSold = ($item->purchase && $item->purchase->exists())
            || (($item->status ?? null) === 'sold');
            @endphp
            <li class="item-card">
                <a class="item-card__link" href="{{ route('items.show', $item) }}">
                    <div class="item-card__image-wrap">
                        @if ($isSold)
                        <span class="item-card__sold">Sold</span>
                        @endif
                        <img class="item-card__image"
                            src="{{ $src }}"
                            alt="{{ $item->title }}"
                            width="290" height="290"
                            loading="lazy"
                            onerror="this.onerror=null; this.src='{{ asset('images/placeholder-290.png') }}';">
                    </div>
                    <div class="item-card__body">
                        <p class="item-card__title">{{ $item->title }}</p>
                    </div>
                </a>
            </li>
            @empty
            <li style="list-style:none; padding:24px 8px; color:#666;">
                出品した商品はまだありません。
            </li>
            @endforelse

            {{-- 購入した商品（常に Sold 表示） --}}
            @elseif ($tab === 'buy' && isset($purchases))
            @forelse ($purchases as $purchase)
            @php
            $it = $purchase->item; // with('item') 推奨
            $src = $img($it->image_path ?? null);
            @endphp
            <li class="item-card">
                <a class="item-card__link" href="{{ $it ? route('items.show', $it) : '#' }}">
                    <div class="item-card__image-wrap">
                        <span class="item-card__sold">Sold</span>
                        <img class="item-card__image"
                            src="{{ $src }}"
                            alt="{{ $it->title ?? '商品' }}"
                            width="290" height="290"
                            loading="lazy"
                            onerror="this.onerror=null; this.src='{{ asset('images/placeholder-290.png') }}';">
                    </div>
                    <div class="item-card__body">
                        <p class="item-card__title">{{ $it->title ?? '商品名' }}</p>
                    </div>
                </a>
            </li>
            @empty
            <li style="list-style:none; padding:24px 8px; color:#666;">
                購入した商品はまだありません。
            </li>
            @endforelse

            {{-- データなし --}}
            @else
            <li style="list-style:none; padding:24px 8px; color:#666;">
                表示できる商品がありません。
            </li>
            @endif
        </ul>

        {{-- ページネーション（クエリ保持） --}}
        @if ($tab === 'sell' && isset($items) && method_exists($items, 'links'))
        <div class="items__pagination">
            {{ $items->withQueryString()->links() }}
        </div>
        @endif
        @if ($tab === 'buy' && isset($purchases) && method_exists($purchases, 'links'))
        <div class="items__pagination">
            {{ $purchases->withQueryString()->links() }}
        </div>
        @endif
    </main>
</div>
@endsection