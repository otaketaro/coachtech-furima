@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/auth.css') }}">
@endsection

@section('content')
<main class="auth-wrap">
    <section class="auth-card authcontent">
        <header class="auth-header">
            <h1 class="auth-title">ログイン</h1>
        </header>

        <form method="POST" action="{{ route('login') }}" novalidate>
            @csrf

            <div class="form-group">
                <label class="label" for="email">メールアドレス</label>
                <input id="email" class="input" name="email" type="text"
                    value="{{ old('email') }}" maxlength="255" autocomplete="email">
                @error('email')
                <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label class="label" for="password">パスワード</label>
                <input id="password" class="input" type="password" name="password"
                    autocomplete="current-password">
                @error('password')
                <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <div class="actions">
                <button class="button" type="submit">ログインする</button>
            </div>

            <p class="alt auth-link">
                <a class="link-blue" href="{{ route('register') }}">会員登録はこちら</a>
            </p>
        </form>
    </section>
</main>
@endsection