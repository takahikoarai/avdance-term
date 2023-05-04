@extends('layouts.default')
<head>
  <link rel="stylesheet" href="css/attendance.css">
  <link rel="stylesheet" href="css/user-attendance.css">
</head>

@section('title', 'ユーザー別勤怠ページ')
@section('content')
  <div class="user-name">
    <p>{{ $userName }}さんの勤怠記録</p>
  </div>
  {{-- 月ごとに表示したい --}}
  {{-- <div class="date">
    <form action="" method="get">
      <button name="" id="prev" value="">&lt;</button>
    </form>
    <p class="date-today">今月</p>
    <form action="" method="get">
      <button name="" id="next" value="">&gt;</button>
    </form>
  </div> --}}
  <div class="result">
    <table class="result-table">
      <tr class="table-title">
        <th>日付</th>
        <th>勤務開始</th>
        <th>勤務終了</th>
        <th>休憩時間</th>
        <th>勤務時間</th>
      </tr>
      @foreach ($attendances as $values)
      <tr class="table-value">
        @foreach ($values as $sub_value)
          <td>{{ $sub_value }}</td>
        @endforeach
      </tr>
      @endforeach
    </table>
  </div>
  <div class="paginate">
    <form action="/attendance" method="get">
      <input type="hidden" name="date">
        {{ $attendances->links() }}
      </form>
  </div>
@endsection