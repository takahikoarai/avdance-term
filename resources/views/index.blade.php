@extends('layouts.default')

@section('title', '打刻ページ')
@section('content')
  <main>
    <div class="main__title">
      @if(Auth::check())
        <p>{{$user->name}}さんお疲れ様です！</p>
      @endif
    </div>
    <div class="main__attendance">
      <div class="attendance__left">
        <form action="/workStart" method="POST" class="timestamp">
          @csrf
          <button class="button1">勤務開始</button>
        </form>
        <form action="/restStart" method="POST" class="timestamp">
          @csrf
          <button class="button2">休憩開始</button>
        </form>
        </div>
      <div class="attendance__right">
        <form action="/workEnd" method="POST" class="timestamp">
          @csrf
          <button class="button3">勤務終了</button>
        </form>
        <form action="/restEnd" method="POST" class="timestamp">
          @csrf
          <button class="button4">休憩終了</button>
        </form>
      </div>
    </div>
  </main>
@endsection