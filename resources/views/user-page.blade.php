@extends('layouts.default')
<head>
  <link rel="stylesheet" href="css/attendance.css">
  <link rel="stylesheet" href="css/user-page.css">
</head>

@section('title', 'ユーザー一覧')
@section('content')
    <div class="user__title">
      <h1>ユーザー一覧</h1>
    </div>
    <div class="result user-list">
      <table class="result__table user-table">
        <tr class="table__title">
          <th>名前</th>
          <th>メールアドレス</th>
        </tr>
        @foreach($users as $user)
        <tr class="table__value" >
            <td>{{ $user->name }}</td>
            <td>{{ $user->email }}</td>
        </tr>
        @endforeach
      </table>
    </div>
    <div class="paginate">
      {{ $users->links() }}
    </div>
@endsection