<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Attendance;
use App\Models\Rest;

class AttendanceController extends Controller
{
    public function index()
    {
        //打刻ページを表示
        if(Auth::check()){
            return view('index');
        }else{
            return redirect('/login');
        }
    }

    public function workStart()
    {
        //勤務開始時間を記録。「勤務開始」と「休憩終了」の文字がグレーに
        $user = Auth::user();
        $oldworkStart = Attendance::where('user_id', $user->id)->latest()->first();

        $oldDay = '';

        //退勤前に2回押せない制御
        if($oldworkStart){
            $oldAttendanceStartTime = new Carbon($oldworkStart->start_time);
            $oldDay = $oldAttendanceStartTime->startOfday();//Carbonインスタンスを生成することで、starOfdayメソッドが使える
            $today = Carbon::today();

            if(($oldDay == $today) && (empty($oldworkStart->end_time))){
                return redirect()->back()->with('message','出勤打刻済みです');
            }
        }

        //退勤後に出勤を押せない制御
        if($oldworkStart){
            $oldAttendanceEndTime = new Carbon($oldworkStart->end_time);
            $oldDay = $oldAttendanceEndTime->startofDay();
        }

        if(($oldDay == $today)){
            return redirect()->back()->with('message','退勤打刻済みです');
        }

        Attendance::create([
            'user_id' => $user->id,
            'date' => Carbon::today(),
            'start_time' => Carbon::now(),
        ]);

        //文字の色を変える

        return redirect()->back();
    }

    public function workEnd()
    {
        
    }

    //レコードがない場合はcreateを実行、すでにレコードがある場合はstart_timeを更新
    public function restStart()
    {
        $user = Auth::user();//ユーザー認証
        $attendance = Attendance::where('user_id', $user->id)->latest()->first();//attendaceテーブルの最新のレコード1件を取得
        $oldrestStart = Rest::where('attendance_id', $attendance->id)->first();//attendanceテーブルのidにひもづくrestテーブルのレコードのうち最新の1件を取得

        //休憩開始を連続で押すのを防ぎたい
        if($oldrestStart->end_time){

        }

        if($attendance->start_time&&$oldrestStart->start_time && !$attendance->end_time){
            //勤務中＆休憩開始データがすでに存在する＆勤務終了していない、ならばstart_timeを更新
            $oldreststart->update([
                'start_time' => Carbon::now()
            ]);
            return redirect()->back();
        }elseif($attendance->start_time && !$oldrestStart->start_time && !$attendance->end_time){
            //勤務中＆休憩開始データが存在しない＆勤務終了していない、ならばレコードを新規作成
            Rest::create([
                'attendance_id' => $attendance->id,
                'start_time' => Carbon::now(),
            ]);
            return redirect()->back();
        }else{
            return redirect()->back();
        }
    }
    
    public function restEnd()
    {
        
    }

    public function attendances()
    {
        
    }

}
