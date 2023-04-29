@extends('layouts.default')
<head>
    <link rel="stylesheet" href="css/register.css">
</head>
@section('title', '会員登録')
@section('content')
<x-guest-layout>
    <x-auth-card>
        <!-- <x-slot name="logo">
            <a href="/">
                <x-application-logo class="w-20 fill-current text-gray-500" />
            </a>
        </x-slot> -->
        <div class="register-title">
            <h1>会員登録</h1>
        </div>
        
        <!-- Validation Errors -->
        <x-auth-validation-errors class="mb-4" :errors="$errors" />

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <!-- Name -->
            <div>
                <!-- <x-label for="name" :value="__('Name')" /> -->

                <x-input id="name" class="block mt-1 w-full bg-gray-main" type="text" name="name" :value="old('name')" required autofocus placeholder="名前"/>
            </div>

            <!-- Email Address -->
            <div class="mt-4">
                <!-- <x-label for="email" :value="__('Email')" /> -->

                <x-input id="email" class="block mt-1 w-full bg-gray-main" type="email" name="email" :value="old('email')" required placeholder="メールアドレス"/>
            </div>

            <!-- Password -->
            <div class="mt-4">
                <!-- <x-label for="password" :value="__('Password')" /> -->

                <x-input id="password" class="block mt-1 w-full bg-gray-main"
                                type="password"
                                name="password"
                                required autocomplete="new-password" placeholder="パスワード"/>
            </div>

            <!-- Confirm Password -->
            <div class="mt-4">
                <!-- <x-label for="password_confirmation" :value="__('Confirm Password')" /> -->

                <x-input id="password_confirmation" class="block mt-1 w-full bg-gray-main"
                                type="password"
                                name="password_confirmation" required placeholder="確認用パスワード"/>
            </div>

            <button class=" button-register">会員登録</button>
            <div class="link-login">
                <!-- <x-button class="button-register">
                    {{ __('Register') }}
                </x-button> -->
                <p class="register-message">アカウントをお持ちの方はこちらから</p>
                <a class="" href="{{ route('login') }}">
                    {{ __('Already registered?') }}
                </a>
            </div>
        </form>
        
    </x-auth-card>
</x-guest-layout>
@endsection