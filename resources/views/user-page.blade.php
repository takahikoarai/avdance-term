@extends('layouts.default')
<head>
  <link rel="stylesheet" href="css/attendance.css">
  <link rel="stylesheet" href="css/user-page.css">
</head>

@section('title', 'ユーザー一覧')
@section('content')
    <div class="user-title">
      <h1>ユーザー一覧</h1>
    </div>
    <div class="result user-list">
      <table class="result-table user-table">
        <tr class="table-title">
          <th>名前</th>
          <th>メールアドレス</th>
        </tr>
        @foreach ($users as $user)
        <form action="/user-attendance" method="get">
          <tr class="table-value table-value-user">
            <td>
              <button  name="name" value="{{ $user->name }}" class="name-button">{{ $user->name }}</button>
            </td>
            <td>{{ $user->email }}</td>
          </tr>
        </form>
        @endforeach
      </table>
    </div>
    <div class="paginate">
      {{ $users->links() }}
    </div>
@endsection