@extends('layouts.default')
<head>
  <link rel="stylesheet" href="css/attendance.css">
  <link rel="stylesheet" href="css/user-attendance.css">
</head>

@section('title', 'ユーザー別勤怠ページ')
@section('content')
  <div class="user-name">
    <p>{{ $userName }}</p>
  </div>
  <div class="date">
    <form action="/attendance" method="get">
      <button name="date" id="prev" value=""><</button>
    </form>
    <p class="date__today">今月</p>
    <form action="/attendance" method="get">
      <button name="date" id="next" value="">></button>
    </form>
  </div>
  <div class="result">
    <table class="result__table">
      <tr class="table__title">
        <th>日付</th>
        <th>勤務開始</th>
        <th>勤務終了</th>
        <th>休憩時間</th>
        <th>勤務時間</th>
      </tr>
      @foreach($attendances as $values)
      <tr class="table__value" >
        @foreach($values as $sub_value)
          <td>{{ $sub_value }}</td>
        @endforeach
      </tr>
      @endforeach
    </table>
  </div>
  <div class="paginate">
    <form action="/attendance" method="get">
      <input type="hidden" name="date" value="">
        {{ $attendances->links() }}
      </form>
  </div>
@endsection