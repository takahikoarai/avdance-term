<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title')</title>
  <link rel="stylesheet" href="css/default.css">
</head>
<body>
    <header>
    <div class="header-inner">
      <div class="header-title">
        <a href="/" class="header-title">Atte</a>
      </div>
      @if (Auth::check())
        <nav>
          <ul>
            <li class="nav-item"><a href="/">ホーム</a></li>
            <li class="nav-item">
              <!-- ここはaタグでよいのでは -->
              <form action="/attendance" method="get">
                <button class="nav-attendance" name="date" value="today">日付一覧</button>
              </form>
            </li>
            <li class="nav-item"><a href="/user-page">ユーザー一覧</a></li>
            <li class="nav-item">
              <form action="{{ route('logout') }}" method="post">
                @csrf
                <button class="nav-logout">ログアウト</button>
              </form>
            </li>
          </ul>
        </nav>
      @endif
    </div>
  </header>
  <main>
    <div class="main-inner">
      @yield('content')
    </div>
  </main>
  <footer>
    <small>Atte, Inc.</small>
  </footer>
  <script src="js/attendance.js"></script>
</body>
</html>