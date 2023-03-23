<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title')</title>
  <link rel="stylesheet" href="css/index.css">
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
            <li class="nav__item"><a href="/attendance">日付一覧</a></li>
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
  @yield('content')
  <footer>
    <small>Atte, Inc.</small>
  </footer>
</body>
</html>