@extends('layouts.default')
<head>
  <link rel="stylesheet" href="css/index.css">
</head>

@section('title', '打刻ページ')
@section('content')
    <div class="main-title">
      @if (Auth::check())
        <p>{{ $user->name }}さんお疲れ様です！</p>
      @endif
    </div>
    @if ($isWorkStarted && $isRestStarted)
      <p class="status">休憩中</p>
    @elseif ($isWorkStarted)
      <p class="status">勤務中</p>
    @else
      <p class="status">出勤前</p>
    @endif
    <div class="main-attendance">
      <div class="attendance-left">
        <!-- 勤務開始 -->
        @if (($isWorkStarted) || ($isWorkEnded))
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
        <!-- 休憩開始 -->
        @if ($isWorkStarted && $isRestStarted)
          <form action="/restStart" method="POST" class="timestamp">
            @csrf
            <button disabled style="color:gray">休憩開始</button>
          </form>
        @elseif ($isWorkStarted)
          <form action="/restStart" method="POST" class="timestamp">
            @csrf
            <button class="button2">休憩開始</button>
          </form>
        @else
          <form action="/restStart" method="POST" class="timestamp">
            @csrf
            <button disabled style="color:gray">休憩開始</button>
          </form>
        @endif
        </div>
      <div class="attendance-right">
        <!-- 勤務終了 -->
        @if ($isWorkStarted)
          <form action="/workEnd" method="POST" class="timestamp">
            @csrf
            <button class="button3">勤務終了</button>
          </form>
        @elseif ($isWorkStarted && $isWorkEnded)
          <form action="/workEnd" method="POST" class="timestamp">
            @csrf
            <button disabled style="color:gray">勤務終了</button>
          </form>
        @else
          <form action="/workEnd" method="POST" class="timestamp">
            @csrf
            <button disabled style="color:gray">勤務終了</button>
          </form>
        @endif
        <!-- 休憩終了 -->
        @if (($isWorkStarted) && ($isRestStarted))
          <form action="/restEnd" method="POST" class="timestamp">
            @csrf
            <button class="button4">休憩終了</button>
          </form>
        @else
          <form action="/restEnd" method="POST" class="timestamp">
            @csrf
            <button disabled style="color:gray">休憩終了</button>
          </form>
        @endif
      </div>
    </div>
@endsection