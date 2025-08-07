<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
class AuthController extends Controller
{
    //
    public function showRegister()
    {
        return view('auth.register');
    }
    public function register(RegisterRequest $request) {
        $user = User::create([
            'name' => $request ->name,
            'email' => $request->email,
            'password' =>Hash::make($request->password),
        ]);
        Auth::login($user);

        return redirect('/mypage/profile');
    }
}
