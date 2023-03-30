@extends('layouts.default')

@section('title', '日付別勤怠ページ')
@section('content')
  <p>ここはattendnace.blade.phpです</p>
  <p>ユーザーID{{ $attendanceToday->user_id }}</p>
  <p>日付{{ $attendanceToday->date }}</p>
  <p>勤務開始{{ $attendanceToday->start_time }}</p>
  <p>勤務終了{{ $attendanceToday->end_time }}</p>
  <p>勤務時間（秒）{{ $workTime }}</p>
@endsection