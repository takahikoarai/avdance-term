@extends('layouts.default')

@section('title', '打刻ページ')
@section('content')
  <header>
    <div class="header__inner">
      <div class="header__title">
        <a href="/" class="header__title">Atte</a>
      </div>
      <nav>
        <ul>
          <li class="nav__item"><a href="/">ホーム</a></li>
          <li class="nav__item"><a href="/attendance">日付一覧</a></li>
          <li class="nav__item"><a href="{{ route('logout') }}">ログアウト</a></li>
        </ul>
      </nav>
    </div>
  </header>
  <main>
    <div class="main__title">
      <p>（ユーザー名）さんお疲れ様です！</p>
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
  <footer>
    <small>Atte, Inc.</small>
  </footer>
@endsection