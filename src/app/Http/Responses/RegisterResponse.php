<?php

namespace App\Http\Responses;

use Laravel\Fortify\Contracts\RegisterResponse as RegisterResponseContract;

class RegisterResponse implements RegisterResponseContract
{
    public function toResponse($request)
    {
        // 登録完了後に /email/verify にリダイレクト
        return redirect()->route('verification.notice');
    }
}
