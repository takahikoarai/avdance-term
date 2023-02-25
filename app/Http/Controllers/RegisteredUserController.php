<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Request\RegisterRequest;

class RegisteredUserController extends Controller
{
    public function create()
    {
        //ユーザー新規登録ページ表示
        return view('register');
    }

    public function store(RegisterRequest $request)
    {
        //ユーザー新規登録処理
        // バリデーションを実行後、usersテーブルにレコード作成し、/loginにリダイレクト
        $user = $requset->all();
        User::create($user);
        return redirect('login');
    }

    public function getIndex()
    {
        //ログインページを表示
        return view('/');
    }

    public function postIndex()
    {
        //ログイン処理
    }

    public function logout()
    {
        //ログアウトしログインページを表示
    }
}
