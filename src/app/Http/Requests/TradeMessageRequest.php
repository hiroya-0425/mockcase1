<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TradeMessageRequest extends FormRequest
{
    public function authorize()
    {
        // 認証済みユーザーのみ許可
        return auth()->check();
    }

    public function rules()
    {
        return [
            'message' => 'required|string|max:400',
            'image'   => 'nullable|image|mimes:jpeg,png|max:5120', // 5MB上限（任意）
        ];
    }

    public function messages()
    {
        return [
            'message.required' => '本文を入力してください',
            'message.max'      => '本文は400文字以内で入力してください',
            'image.mimes'      => '「.png」または「.jpeg」形式でアップロードしてください',
        ];
    }
}
