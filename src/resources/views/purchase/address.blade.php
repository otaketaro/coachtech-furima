{{-- resources/views/purchase/address.blade.php --}}
@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/profile-edit.css') }}?v={{ time() }}">
@endsection

@section('content')
@php
$user = auth()->user();
@endphp

<main class="pfx">
    <h1 class="pfx__title">住所の変更</h1>

    <form class="pfx__form" method="POST" action="{{ route('purchase.address.update', $item) }}">
        @csrf
        @method('PUT')

        {{-- 郵便番号 --}}
        <div class="pfx__group">
            <label for="shipping_postal_code" class="pfx__label">郵便番号</label>
            <input
                id="shipping_postal_code"
                name="shipping_postal_code"
                type="text"
                class="pfx__input"
                value="{{ old('shipping_postal_code', $user->postal_code) }}"
                placeholder="例）123-4567">
            @error('shipping_postal_code')
            <p class="pfx__error">{{ $message }}</p>
            @enderror
        </div>

        {{-- 住所 --}}
        <div class="pfx__group">
            <label for="shipping_address" class="pfx__label">住所</label>
            <input
                id="shipping_address"
                name="shipping_address"
                type="text"
                class="pfx__input"
                value="{{ old('shipping_address', $user->address) }}"
                placeholder="都道府県・市区町村・番地など">
            @error('shipping_address')
            <p class="pfx__error">{{ $message }}</p>
            @enderror
        </div>

        {{-- 建物名（任意） --}}
        <div class="pfx__group">
            <label for="shipping_building" class="pfx__label">建物名</label>
            <input
                id="shipping_building"
                name="shipping_building"
                type="text"
                class="pfx__input"
                value="{{ old('shipping_building', $user->building) }}"
                placeholder="建物名・部屋番号など（任意）">
            @error('shipping_building')
            <p class="pfx__error">{{ $message }}</p>
            @enderror
        </div>

        <div class="pfx__actions">
            <button class="pfx__btnPrimary" type="submit">更新する</button>
        </div>
    </form>
</main>
@endsection