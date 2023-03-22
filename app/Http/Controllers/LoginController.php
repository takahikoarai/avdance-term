<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Auth\LoginRequest;

class LoginController extends Controller
{
    public function getIndex()
    {
        //ログインページを表示
        return view('auth.login');
    }

    public function postIndex(LoginRequest $request)
    {
        $email = $request->email;
        $password = $request->password;
        if (Auth::attempt([
            'email' => $email,
            'password' => $password
        ])){
            //ログイン成功
            return redirect('/');
        } else {
            //ログイン失敗
            $text = 'ログインに失敗しました';
            return redirect('/login', ['text' => $text]);
        }
    }
}
