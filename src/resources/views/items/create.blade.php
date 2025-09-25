@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/items-create.css') }}?v={{ time() }}">
@endsection

@section('content')
    <main class="sell">
        <h1 class="sell__title">商品の出品</h1>

        {{-- バリデーションエラー --}}
        @if ($errors->any())
            <div class="sell__errors">
                <ul>
                    @foreach ($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form class="sell__form" action="{{ route('items.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            {{-- 商品画像 --}}
            <section class="block">
                <h2 class="block__label">商品画像</h2>

                <div class="dropzone">
                    <input id="image" type="file" name="image" accept="image/*" required hidden>
                    <label for="image" class="dropzone__button">画像を選択する</label>
                </div>
                @error('image')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </section>

            {{-- 商品の詳細 --}}
            <section class="block">
                <h2 class="block__header">商品の詳細</h2>

                {{-- カテゴリー（複数） --}}
                <div class="field">
                    <div class="field__label">カテゴリー</div>

                    {{-- チップ風チェックボックス（CSS: .chips / .chip を利用） --}}
                    <div class="chips">
                        @foreach ($categories as $cat)
                            <label class="chip">
                                <input type="checkbox" name="categories[]" value="{{ $cat->id }}"
                                    {{ in_array($cat->id, old('categories', [])) ? 'checked' : '' }}>
                                {{ $cat->name }}
                            </label>
                        @endforeach
                    </div>

                    @error('categories')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                    @error('categories.*')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                {{-- 商品の状態 --}}
                <div class="field">
                    <div class="field__label">商品の状態</div>
                    <select name="condition" class="select" required>
                        <option value="" disabled {{ old('condition') ? '' : 'selected' }}>選択してください</option>
                        <option value="new" {{ old('condition') === 'new' ? 'selected' : '' }}>新品・未使用</option>
                        <option value="like_new" {{ old('condition') === 'like_new' ? 'selected' : '' }}>未使用に近い</option>
                        <option value="good" {{ old('condition') === 'good' ? 'selected' : '' }}>目立った傷や汚れなし</option>
                        <option value="fair" {{ old('condition') === 'fair' ? 'selected' : '' }}>やや傷や汚れあり</option>
                        <option value="poor" {{ old('condition') === 'poor' ? 'selected' : '' }}>全体的に状態が悪い</option>
                    </select>
                    @error('condition')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>
            </section>

            {{-- 商品名と説明 --}}
            <section class="block">
                <h2 class="block__header">商品名と説明</h2>

                <div class="field">
                    <div class="field__label">商品名</div>
                    <input class="input" type="text" name="title" value="{{ old('title') }}" required>
                    @error('title')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="field">
                    <div class="field__label">ブランド名</div>
                    <input class="input" type="text" name="brand" value="{{ old('brand') }}">
                    @error('brand')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="field">
                    <div class="field__label">商品の説明</div>
                    <textarea class="textarea" name="description" rows="5" required>{{ old('description') }}</textarea>
                    @error('description')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>
            </section>

            {{-- 価格 --}}
            <section class="block">
                <div class="field">
                    <div class="field__label">販売価格</div>
                    <div class="price">
                        <span class="price__prefix">¥</span>
                        <input class="price__input" type="number" name="price" value="{{ old('price') }}"
                            min="1" required>
                    </div>
                    @error('price')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>
            </section>

            <div class="actions">
                <button class="btn-primary" type="submit">出品する</button>
            </div>
        </form>
    </main>
@endsection
