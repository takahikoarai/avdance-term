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
    <div class="header__inner">
      <div class="header__title">
        <a href="/" class="header__title">Atte</a>
      </div>
      @if(Auth::check())
        <nav>
          <ul>
            <li class="nav__item"><a href="/">ホーム</a></li>
            <li class="nav__item">
              <!-- ここはaタグでよいのでは -->
              <form action="/attendance" method="get">
                <button class="nav__attendance" name="getToday" value="today">日付一覧</button>
              </form>
            </li>
            <li class="nav__item">
              <form action="{{route('logout')}}" method="post">
                @csrf
                <button class="nav__logout">ログアウト</button>
            </li>
            </form>
          </ul>
        </nav>
      @endif
    </div>
  </header>
  <main>
    <div class="main__inner">
      @yield('content')
    </div>
  </main>
  <footer>
    <small>Atte, Inc.</small>
  </footer>
  <script src="js/attendance.js"></script>
</body>
</html>