<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePurchaseRequest extends FormRequest
{
    public function authorize(): bool
    {
        // ルート/コントローラ側で auth ミドルウェアを掛ける前提
        return true;
    }

    /**
     * 画面値 → 内部仕様へ非破壊で正規化
     * convenience -> convenience_store
     * credit      -> card
     */
    protected function prepareForValidation(): void
    {
        $map = [
            'convenience' => 'convenience_store',
            'credit' => 'card',
        ];
        $pm = $this->input('payment_method');
        if (is_string($pm) && isset($map[$pm])) {
            $this->merge(['payment_method' => $map[$pm]]);
        }
    }

    public function rules(): array
    {
        return [
            'payment_method' => ['required', Rule::in(['convenience_store', 'card'])],
            'shipping_postal_code' => ['required', 'max:16'], // 後で regex に強化OK
            'shipping_address' => ['required', 'max:255'],
            'shipping_building' => ['nullable', 'max:255'],
        ];
    }

    public function attributes(): array
    {
        return [
            'payment_method' => 'お支払い方法',
            'shipping_postal_code' => '郵便番号',
            'shipping_address' => '住所',
            'shipping_building' => '建物名',
        ];
    }
}
