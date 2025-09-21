<!doctype html>
<html lang="ja">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'coachtech-furima' }}</title>

    {{-- CSS 読み込み順：リセット → 共通 → ヘッダー → ページ固有 --}}
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}?v=1">
    <link rel="stylesheet" href="{{ asset('css/header.css') }}?v=1">

    {{-- “×”を確実に表示する最小限の保険（WebKit向け） --}}
    <style>
        .site-header__search-input[type="search"] {
            -webkit-appearance: searchfield;
            appearance: auto;
        }

        .site-header__search-input[type="search"]::-webkit-search-cancel-button {
            -webkit-appearance: searchfield-cancel-button;
        }

        .site-header__search-input {
            padding-right: 2.25rem;
        }
    </style>

    @yield('css')
</head>

<body>
    <header class="site-header">
        <div class="site-header__inner">
            {{-- ロゴ --}}
            <a class="site-header__logo" href="{{ url('/') }}">
                <img src="{{ asset('images/logo-coachtech-white.png') }}" alt="COACHTECH">
            </a>

            {{-- ログイン・会員登録ページ以外では検索フォームとナビを表示 --}}
            @unless (request()->routeIs(['login','register']))
            {{-- 検索フォーム --}}
            <form method="GET"
                action="{{ route('items.index') }}"
                class="site-header__search"
                id="site-search-form"
                role="search">
                <input type="hidden" name="tab" value="{{ request('tab') }}">
                <label class="sr-only" for="header-search">商品名で検索</label>
                <input
                    id="header-search"
                    type="search" {{-- ネイティブの“×”が出る --}}
                    name="q"
                    value="{{ request('q') }}"
                    placeholder="なにをお探しですか？"
                    aria-label="商品名で検索"
                    class="site-header__search-input">
                {{-- 余計な「クリア」リンクは削除 --}}
                <input type="submit" hidden aria-hidden="true">
            </form>

            {{-- ナビゲーション --}}
            <nav class="site-header__nav" aria-label="ヘッダーナビゲーション">
                @guest
                <a href="{{ route('login') }}" class="site-header__nav-link">ログイン</a>
                <a href="{{ route('login') }}" class="site-header__nav-link">マイページ</a>
                <a href="{{ route('login') }}" class="site-header__nav-sell">出品</a>
                @endguest

                @auth
                <form method="POST" action="{{ route('logout') }}" style="display:inline">
                    @csrf
                    <button type="submit" class="site-header__logout">ログアウト</button>
                </form>
                <a href="{{ route('mypage.index') }}" class="site-header__nav-link">マイページ</a>
                <a href="{{ route('items.create') }}" class="site-header__nav-sell">出品</a>
                @endauth
            </nav>

            {{-- “×”や全文削除で空になったら即送信して検索状態をリセット --}}
            <script>
                (function() {
                    var input = document.getElementById('header-search');
                    var form = document.getElementById('site-search-form');
                    if (!input || !form) return;

                    // “×”押下やエンター時（type=searchの標準イベント）
                    input.addEventListener('search', function() {
                        if (input.value === '') form.submit();
                    });
                    // 手入力で消し切ったときも即反映したい場合
                    input.addEventListener('input', function() {
                        if (input.value === '') {
                            requestAnimationFrame(function() {
                                form.submit();
                            });
                        }
                    });
                })();
            </script>
            @endunless
        </div>
    </header>

    {{-- フラッシュメッセージ --}}
    @if (session('status'))
    <div class="flash-status">{{ session('status') }}</div>
    @endif

    @yield('content')
</body>

</html>