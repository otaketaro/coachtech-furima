@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/auth.css') }}">
@endsection

@section('content')
<main class="auth-wrap">
    <section class="auth-card authcontent">
        <header class="auth-header">
            <h1 class="auth-title">会員登録</h1>
            <p class="auth-sub"></p>
        </header>

        {{-- サーバー側のバリデーションを見たいので novalidate はこのままでOK --}}
        <form method="POST" action="{{ route('register') }}" novalidate>
            @csrf

            <div class="form-group">
                <label class="label" for="name">ユーザー名</label>
                <input
                    id="name"
                    class="input input-lg"
                    name="name"
                    type="text"
                    value="{{ old('name') }}"
                    maxlength="20" {{-- ← 要件：20文字以内に合わせる --}}
                    autocomplete="name">
                @error('name') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label class="label" for="email">メールアドレス</label>
                <input
                    id="email"
                    class="input input-lg"
                    name="email"
                    type="email" {{-- ← email 型にして入力補助＆軽い検証 --}}
                    value="{{ old('email') }}"
                    maxlength="255"
                    autocomplete="email">
                @error('email') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label class="label" for="password">パスワード</label>
                <input
                    id="password"
                    class="input input-lg"
                    type="password"
                    name="password"
                    autocomplete="new-password">
                @error('password') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label class="label" for="password_confirmation">確認用パスワード</label>
                <input
                    id="password_confirmation"
                    class="input input-lg"
                    type="password"
                    name="password_confirmation"
                    autocomplete="new-password">
                {{-- 任意：確認用の個別メッセージを出すなら下の @error を有効化
                @error('password_confirmation') <div class="error">{{ $message }}
            </div> @enderror
            --}}
            </div>

            <div class="actions">
                <button class="button btn-primary-red btn-680" type="submit">登録する</button>
            </div>

            <p class="alt auth-link">
                <a class="link-blue" href="{{ route('login') }}">ログインはこちら</a>
            </p>
        </form>
    </section>
</main>
@endsection