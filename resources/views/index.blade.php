@extends('layouts.default')

@section('title', '打刻ページ')
@section('content')
  <main>
    <div class="main__title">
      @if(Auth::check())
        <p>{{$user->name}}さんお疲れ様です！</p>
      @endif
    </div>
    <p class="error">{{ session('message') }}</p>
    <div class="main__attendance">
      <div class="attendance__left">
        @if($isWorkStarted || $isWorkEnded)
        <!-- グレーアウトさせる処理を追加する -->
          <form action="/workStart" method="POST" class="timestamp">
            @csrf
            <button disabled style="color:gray">勤務開始</button>
          </form>
        @else
          <form action="/workStart" method="POST" class="timestamp">
            @csrf
            <button class="button1">勤務開始</button>
          </form>
        @endif
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