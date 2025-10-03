{{-- resources/views/purchase/create.blade.php --}}
@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/purchase.css') }}?v={{ time() }}">
@endsection

@section('content')
    @php
        /** 画像URLの解決（外部URL or storage） */
        $p = $item->image_path ?? null;
        $isUrl = is_string($p) && preg_match('/^https?:\/\//i', $p);
        $src = $isUrl ? $p : ($p ? asset('storage/' . $p) : asset('images/placeholder-290.png'));

        /** 支払い方法（初期表示ラベル） */
        $pmOld = old('payment_method');
        $pmMap = [
            'convenience_store' => 'コンビニ支払い',
            'card' => 'カード支払い',
        ];
        $pmLabelInit = $pmOld ? $pmMap[$pmOld] ?? '選択してください' : '選択してください';

        /** $prefill セーフガード */
        $postal = $prefill['shipping_postal_code'] ?? '';
        $address = $prefill['shipping_address'] ?? '';
        $building = $prefill['shipping_building'] ?? '';
    @endphp

    <main class="pg-purchase">

        {{-- バリデーションエラー表示 --}}
        @if ($errors->any())
            <div class="flash-error">
                <ul style="margin:0 0 8px 16px;">
                    @foreach ($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="purchase-grid">
            {{-- ===== 左カラム ===== --}}
            <section>
                {{-- 商品情報 --}}
                <div class="block product-row">
                    <div class="product-thumb">
                        <img src="{{ $src }}" alt="{{ $item->title }}"
                            onerror="this.onerror=null; this.src='{{ asset('images/placeholder-290.png') }}';">
                    </div>
                    <div class="product-meta">
                        <h1 class="product-title">{{ $item->title }}</h1>
                        <div class="product-price">¥ {{ number_format($item->price) }}</div>
                    </div>
                </div>

                <div class="rule" aria-hidden="true"></div>

                {{-- ✅ 購入フォーム（ID固定＆CSRF、送信先固定） --}}
                <form id="purchase-form" method="POST" action="{{ route('purchase.store', $item) }}" novalidate>
                    @csrf

                    {{-- 支払い方法 --}}
                    <div class="block">
                        <h2 class="block-title">支払い方法</h2>
                        <div class="pay-select-wrap">
                            <select name="payment_method" id="payment_method" class="pay-select" required>
                                <option value="" disabled {{ $pmOld ? '' : 'selected' }}>選択してください</option>
                                <option value="convenience_store" {{ $pmOld === 'convenience_store' ? 'selected' : '' }}>
                                    コンビニ支払い</option>
                                <option value="card" {{ $pmOld === 'card' ? 'selected' : '' }}>カード支払い</option>
                            </select>
                        </div>
                        @error('payment_method')
                            <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="rule" aria-hidden="true"></div>

                    {{-- 配送先（セッション or プロフィールからの $prefill を使用） --}}
                    <div class="block">
                        <div class="address-head">
                            <h2 class="block-title" style="margin:0;">配送先</h2>
                            <a class="address-link" href="{{ route('purchase.address.edit', $item) }}">変更する</a>
                        </div>

                        {{-- 郵便番号は1行 --}}
                        <div class="address-line">〒 {{ $postal }}</div>

                        {{-- 住所＋建物は横並び（建物は任意） --}}
                        <div class="address-pair">
                            <span class="address-line">{{ $address }}</span>
                            @if ($building)
                                <span class="address-line">{{ $building }}</span>
                            @endif
                        </div>
                    </div>

                    {{-- hidden（送信用に $prefill をそのまま流す） --}}
                    <input type="hidden" name="shipping_postal_code" value="{{ $postal }}">
                    <input type="hidden" name="shipping_address" value="{{ $address }}">
                    <input type="hidden" name="shipping_building" value="{{ $building }}">
                </form>
            </section>

            {{-- ===== 右カラム ===== --}}
            <aside class="purchase-aside">
                <div class="summary-card">
                    <div class="summary-row">
                        <div class="summary-label">商品代金</div>
                        <div class="summary-value">¥ {{ number_format($item->price) }}</div>
                    </div>
                    <div class="summary-row">
                        <div class="summary-label">支払い方法</div>
                        <div class="summary-value" id="payment_summary">{{ $pmLabelInit }}</div>
                    </div>
                </div>

                {{-- フォームの外でも確実に purchase-form を送信（logoutフォーム誤爆対策） --}}
                <button type="submit" form="purchase-form" class="btn-purchase">購入する</button>
            </aside>
        </div>
    </main>

    {{-- 支払い方法の表示同期 & 一度選択後にプレースホルダー除去 --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const sel = document.getElementById('payment_method');
            const out = document.getElementById('payment_summary');
            if (!sel || !out) return;

            const labelMap = {
                'convenience_store': 'コンビニ支払い',
                'card': 'カード支払い'
            };
            let removedPlaceholder = false;

            function sync() {
                const v = sel.value;
                out.textContent = labelMap[v] ?? '選択してください';

                if (!removedPlaceholder && v) {
                    const ph = sel.querySelector('option[value=""]');
                    if (ph) ph.remove();
                    removedPlaceholder = true;
                }
            }
            sel.addEventListener('change', sync);
            sync();
        });
    </script>
@endsection
