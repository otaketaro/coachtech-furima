{{-- resources/views/mypage/edit.blade.php --}}
@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/profile-edit.css') }}?v=1">
@endsection

@section('content')
    <div class="pfx">

        <h1 class="pfx__title">プロフィール設定</h1>

        {{-- ※ フラッシュメッセージは layouts/app.blade.php 側で1回だけ表示するため、このページでは出しません --}}

        <form method="POST" action="{{ route('mypage.profile.update') }}" enctype="multipart/form-data" class="pfx__form">
            @csrf
            @method('PUT')

            {{-- アバター --}}
            <div class="pfx__avatarRow">
                @php use Illuminate\Support\Facades\Storage; @endphp
                @if ($user->avatar_path)
                    <img src="{{ Storage::url($user->avatar_path) }}?v={{ time() }}" alt="avatar"
                        class="pfx__avatarImg">
                @else
                    <div class="pfx__avatarPh"></div>
                @endif

                <label for="avatar" class="pfx__btnGhost">画像を変更</label>
                <input id="avatar" type="file" name="avatar" accept=".jpeg,.jpg,.png" class="pfx__file">
            </div>
            @error('avatar')
                <p class="pfx__error">{{ $message }}</p>
            @enderror

            {{-- ユーザー名 --}}
            <div class="pfx__group">
                <label class="pfx__label">ユーザー名</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" class="pfx__input">
                @error('name')
                    <p class="pfx__error">{{ $message }}</p>
                @enderror
            </div>

            {{-- 郵便番号 --}}
            <div class="pfx__group">
                <label class="pfx__label">郵便番号</label>
                <input type="text" name="postal_code" value="{{ old('postal_code', $user->postal_code) }}"
                    placeholder="123-4567" class="pfx__input">
                @error('postal_code')
                    <p class="pfx__error">{{ $message }}</p>
                @enderror
            </div>

            {{-- 住所 --}}
            <div class="pfx__group">
                <label class="pfx__label">住所</label>
                <input type="text" name="address" value="{{ old('address', $user->address) }}" class="pfx__input">
                @error('address')
                    <p class="pfx__error">{{ $message }}</p>
                @enderror
            </div>

            {{-- 建物名 --}}
            <div class="pfx__group">
                <label class="pfx__label">建物名</label>
                <input type="text" name="building" value="{{ old('building', $user->building) }}" class="pfx__input">
                @error('building')
                    <p class="pfx__error">{{ $message }}</p>
                @enderror
            </div>

            {{-- 保存ボタン --}}
            <div class="pfx__actions">
                <button type="submit" class="pfx__btnPrimary">更新する</button>
            </div>
        </form>
    </div>
@endsection
