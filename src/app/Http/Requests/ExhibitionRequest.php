<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExhibitionRequest extends FormRequest
{
    public function authorize(): bool
    {
        // ← ここが false だと必ず弾かれて / に戻ります
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'brand' => ['nullable', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:255'],
            'price' => ['required', 'integer', 'min:1'],
            'condition' => ['required', 'string'],

            // 画像：GDなし環境でも通るように "image" は使わず、mimes で判定
            // テストで UploadedFile::fake()->image() でも create(..., 'image/jpeg') でも通ります
            'image' => ['required', 'file', 'mimes:jpeg,jpg,png'],

            // カテゴリ必須・最低1つ
            'categories' => ['required', 'array', 'min:1'],
            'categories.*' => ['integer', 'exists:categories,id'],
        ];
    }

    public function attributes(): array
    {
        return [
            'title' => '商品名',
            'brand' => 'ブランド名',
            'description' => '商品説明',
            'price' => '価格',
            'condition' => '商品の状態',
            'image' => '商品画像',
            'categories' => '商品カテゴリー',
            'categories.*' => '商品カテゴリー',
        ];
    }
}
