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

        Attendance::create([
            'user_id' => $user->id,
            'date' => Carbon::today(),
            'start_time' => Carbon::now(),
        ]);

        //文字の色を変える
        //同じに1回しか押せないようにするにはどうする

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
        $oldreststart = Rest::where('attendance_id', $attendance->id)->first();//attendanceテーブルのidにひもづくrestテーブルのレコードのうち最新の1件を取得

        //休憩開始を連続で押すのを防ぎたい

        if($attendance->start_time &&$oldreststart->start_time && !$attendance->end_time){
            //勤務中＆休憩開始データがすでに存在する＆勤務終了していない、ならばstart_timeを更新
            $oldreststart->update([
                'start_time' => Carbon::now()
            ]);
            return redirect()->back();
        }elseif($attendance->start_time && !$oldreststart->start_time && !$attendance->end_time){
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
