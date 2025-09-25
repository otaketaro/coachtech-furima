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

            {{-- サーバー側のバリデーション挙動を確認したいので HTML5 の自動検証は抑制 --}}
            <form method="POST" action="{{ route('register') }}" novalidate aria-label="会員登録フォーム">
                @csrf

                {{-- ユーザー名（20文字以内） --}}
                <div class="form-group">
                    <label class="label" for="name">ユーザー名</label>
                    <input id="name" class="input input-lg" name="name" type="text" value="{{ old('name') }}"
                        maxlength="20" autocomplete="name" required aria-required="true">
                    @error('name')
                        <div class="error" role="alert">{{ $message }}</div>
                    @enderror
                </div>

                {{-- メールアドレス（255文字以内・形式検証） --}}
                <div class="form-group">
                    <label class="label" for="email">メールアドレス</label>
                    <input id="email" class="input input-lg" name="email" type="email" value="{{ old('email') }}"
                        maxlength="255" autocomplete="email" required aria-required="true">
                    @error('email')
                        <div class="error" role="alert">{{ $message }}</div>
                    @enderror
                </div>

                {{-- パスワード（8文字以上） --}}
                <div class="form-group">
                    <label class="label" for="password">パスワード</label>
                    <input id="password" class="input input-lg" type="password" name="password" autocomplete="new-password"
                        required aria-required="true">
                    @error('password')
                        <div class="error" role="alert">{{ $message }}</div>
                    @enderror
                </div>

                {{-- 確認用パスワード（password と一致） --}}
                <div class="form-group">
                    <label class="label" for="password_confirmation">確認用パスワード</label>
                    <input id="password_confirmation" class="input input-lg" type="password" name="password_confirmation"
                        autocomplete="new-password" required aria-required="true">
                    {{-- バリデーション実装によっては password 側に attached される場合もあるため両方ケア --}}
                    @error('password_confirmation')
                        <div class="error" role="alert">{{ $message }}</div>
                    @enderror
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
