@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/item-show.css') }}?v=8">
@endsection

@section('content')
    <div class="pg-item-show">
        <div class="pg-item-show__underline"></div>

        <div class="pg-item-show__grid product-detail">
            {{-- 左：画像＋Soldバッジ --}}
            <div class="item-visual product-image-area">
                <div class="item-visual__box">
                    @php
                        // 画像パス：フルURL or storage相対パス or プレースホルダ
                        $p = $item->image_path; // DBの値
                        $isUrl = is_string($p) && preg_match('/^https?:\/\//i', $p);
                        $src = $isUrl ? $p : ($p ? asset('storage/' . $p) : asset('images/placeholder-290.png'));
                    @endphp

                    <img src="{{ $src }}" alt="{{ $item->title }}" class="item-visual__img"
                        onerror="this.onerror=null; this.src='{{ asset('images/placeholder-290.png') }}';">

                    @if (!empty($item->is_sold))
                        <span class="badge-sold">Sold</span>
                    @endif
                </div>
            </div>

            {{-- 右：商品情報 --}}
            <div class="item-info product-description-area">
                <h1 class="item-info__title">{{ $item->title }}</h1>

                {{-- ブランド名 --}}
                <dl class="item-spec">
                    <div class="item-spec__row">
                        <dt>ブランド名</dt>
                        <dd>{{ $item->brand }}</dd>
                    </div>
                </dl>

                {{-- 価格（税込） --}}
                <div class="item-info__price">
                    ¥{{ number_format($item->price) }} <span>（税込）</span>
                </div>

                {{-- メトリクス（いいね／コメント数） --}}
                <div class="item-metrics">
                    {{-- いいね --}}
                    <div class="metric">
                        @php
                            // コントローラで $isLiked を渡していない場合の保険
                            $isLiked =
                                $isLiked ??
                                (auth()->check()
                                    ? (bool) optional($item->likes)->firstWhere('user_id', auth()->id())
                                    : false);
                            $likesCount = $item->likes_count ?? (optional($item->likes)->count() ?? 0);
                        @endphp

                        @auth
                            <form action="{{ $isLiked ? route('likes.destroy', $item) : route('likes.store', $item) }}"
                                method="post" class="metric-like-form">
                                @csrf
                                @if ($isLiked)
                                    @method('DELETE')
                                @endif
                                <button type="submit" class="metric-btn {{ $isLiked ? 'is-active' : '' }}"
                                    aria-pressed="{{ $isLiked ? 'true' : 'false' }}"
                                    aria-label="{{ $isLiked ? 'いいねを解除' : 'いいねする' }}">
                                    <img class="metric__img"
                                        src="{{ asset($isLiked ? 'images/icon-star-active.png' : 'images/icon-star.png') }}"
                                        width="40" height="40" alt="">
                                    <span class="metric__num">{{ $likesCount }}</span>
                                </button>
                            </form>
                        @else
                            <a href="{{ route('login') }}" class="metric-btn is-ghost" aria-label="ログインしていいね">
                                <img class="metric__img" src="{{ asset('images/icon-star.png') }}" width="40"
                                    height="40" alt="">
                                @php $likesCount = $item->likes_count ?? optional($item->likes)->count() ?? 0; @endphp
                                <span class="metric__num">{{ $likesCount }}</span>
                            </a>
                        @endauth
                    </div>

                    {{-- コメント数（表示のみ） --}}
                    <div class="metric">
                        <img class="metric__img" src="{{ asset('images/icon-bubble.png') }}" width="40" height="40"
                            alt="">
                        <span
                            class="metric__num">{{ $item->comments_count ?? (optional($item->comments)->count() ?? 0) }}</span>
                    </div>
                </div>

                {{-- 購入動線（ゲストでも表示。リンク先で認証要求） --}}
                <div class="item-actions">
                    @php
                        $isOwner = auth()->check() && (int) auth()->id() === (int) optional($item->seller)->id;
                    @endphp

                    @if (!empty($item->is_sold))
                        <button class="btn-buy" disabled>購入済みです</button>
                    @elseif ($isOwner)
                        <button class="btn-buy" disabled>自分の出品は購入できません</button>
                    @else
                        <a class="btn-buy" href="{{ route('purchase.create', $item) }}">購入手続きへ</a>
                    @endif
                </div>

                {{-- 商品説明 --}}
                <section class="item-desc">
                    <h2 class="sec-title">商品説明</h2>
                    <p class="item-desc__text">{{ $item->description }}</p>
                </section>

                {{-- 商品の情報（カテゴリー / 商品の状態） --}}
                <section class="item-info-extra">
                    <h2 class="sec-title">商品の情報</h2>
                    <dl class="item-spec">
                        <div class="item-spec__row">
                            <dt>カテゴリー</dt>
                            <dd>
                                @forelse($item->categories ?? [] as $c)
                                    <span class="chip">{{ $c->name }}</span>
                                @empty
                                    なし
                                @endforelse
                            </dd>
                        </div>
                        <div class="item-spec__row">
                            <dt>商品の状態</dt>
                            <dd>{{ $item->condition_label }}</dd>
                        </div>
                    </dl>
                </section>

                {{-- コメント一覧 --}}
                <section class="item-comments">
                    @php $commentCount = $item->comments_count ?? optional($item->comments)->count() ?? 0; @endphp
                    <h2 class="sec-title">コメント（{{ $commentCount }}）</h2>

                    @if (empty($item->comments) || $commentCount === 0)
                        {{-- コメントなし --}}
                    @else
                        <ul class="comment-list">
                            @foreach ($item->comments as $comment)
                                <li class="comment">
                                    <div class="comment__avatar">
                                        @if (!empty($comment->user->avatar_path))
                                            <img src="{{ asset('storage/' . $comment->user->avatar_path) }}"
                                                alt="{{ $comment->user->name }}">
                                        @else
                                            <div class="avatar--placeholder">{{ mb_substr($comment->user->name, 0, 1) }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="comment__body">
                                        <div class="comment__meta">
                                            <span class="comment__user">{{ $comment->user->name }}</span>
                                            <span
                                                class="comment__time">{{ optional($comment->created_at)->format('Y/m/d H:i') }}</span>
                                        </div>
                                        <p class="comment__content">{{ $comment->content }}</p>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </section>

                {{-- コメント投稿（未ログインでもUI表示：disabled＋ログイン導線） --}}
                <section class="item-comment-form">
                    <h2 class="sec-title">商品へのコメント</h2>

                    @guest
                        <form class="form form--guest" onsubmit="return false;">
                            <textarea rows="6" disabled></textarea>
                            <a class="btn-primary" href="{{ route('login') }}">コメントを送信する</a>
                        </form>
                    @else
                        <form action="{{ route('comments.store', $item) }}" method="post" class="form">
                            @csrf
                            <textarea name="content" rows="6" placeholder="">{{ old('content') }}</textarea>
                            @error('content')
                                <p class="form-error">{{ $message }}</p>
                            @enderror
                            <button type="submit" class="btn-primary">コメントを送信する</button>
                        </form>
                    @endguest
                </section>
            </div> {{-- /.item-info --}}
        </div> {{-- /.pg-item-show__grid --}}
    </div>
@endsection
