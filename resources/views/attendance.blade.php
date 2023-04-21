@extends('layouts.default')
<head>
  <link rel="stylesheet" href="css/attendance.css">
</head>

@section('title', '日付別勤怠ページ')
@section('content')
    <div class="date">
      <form action="/attendance" method="get">
        <button name="getToday" id="prev" value="{{ $today }}"><</button>
        <!-- <input type="hidden" name="changeDay" value="prev"> -->
      </form>
      <p class="date__today">{{ $today }}</p>
      <form action="/attendance" method="get">
        <button name="getToday" id="next" value="{{ $today }}">></button>
        <!-- <input type="hidden" name="changeDay" value="next"> -->
      </form>
    </div>
    <div class="result">
      <table class="result__table">
        <tr class="table__title">
          <th>名前</th>
          <th>勤務開始</th>
          <th>勤務終了</th>
          <th>休憩時間</th>
          <th>勤務時間</th>
        </tr>
        @foreach($attendances as $values)
        <tr class="table__value">
          @foreach($values as $sub_value)
            <td>{{ $sub_value }}</td>
          @endforeach
        </tr>
        @endforeach
      </table>
    </div>
    <div class="paginate">
      <form action="/attendance" method="get">
        <input type="hidden" name="getToday" value="{{ $today }}">
          {{ $attendances->links() }}
        </form>
    </div>
@endsection