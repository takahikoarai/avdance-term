@extends('layouts.default')
<head>
  <link rel="stylesheet" href="css/attendance.css">
</head>

@section('title', '日付別勤怠ページ')
@section('content')
  <p>ここはattendnace.blade.phpです</p>
  <main>
    <div class="date"></div>
    <div class="result">
      <table>
        <tr>
          <th>名前</th>
          <th>勤務開始</th>
          <th>勤務終了</th>
          <th>休憩時間</th>
          <th>勤務時間</th>
        </tr>
        @foreach($resultArray as $values)
        <!-- $valueには配列が格納されている、と思う -->
        <tr>
          @foreach($values as $sub_value)
            <td>{{ $sub_value }}</td>
          @endforeach
        </tr>
        @endforeach
      </table>
    </div>
    <div class="pagenate"></div>
  </main>
@endsection