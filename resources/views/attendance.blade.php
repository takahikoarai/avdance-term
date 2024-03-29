@extends('layouts.default')
<head>
  <link rel="stylesheet" href="css/attendance.css">
</head>

@section('title', '日付別勤怠ページ')
@section('content')
    <div class="date">
      <form action="/attendance" method="get">
        <button name="date" id="prev" value="{{ $today }}">&lt;</button>
      </form>
      <p class="date-today">{{ $today }}</p>
      <form action="/attendance" method="get">
        <button name="date" id="next" value="{{ $today }}">&gt;</button>
      </form>
    </div>
    <div class="result">
      <table class="result-table">
        <tr class="table-title">
          <th>名前</th>
          <th>勤務開始</th>
          <th>勤務終了</th>
          <th>休憩時間</th>
          <th>勤務時間</th>
        </tr>
        @foreach($attendances as $values)
        <form action="/user-attendance" method="get">
          <tr class="table-value table-value-info" >
            @foreach($values as $sub_value)
            <td>
                <button  name="name" value="{{ $sub_value }}" class="name-button">{{ $sub_value }}</button></td>
          @endforeach
          </tr>
        </form>
        @endforeach
      </table>
    </div>
    <div class="paginate">
      <form action="/attendance" method="get">
        <input type="hidden" name="date" value="{{ $today }}">
          {{ $attendances->links() }}
        </form>
    </div>
@endsection