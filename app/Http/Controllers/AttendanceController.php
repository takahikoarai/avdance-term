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
            $user = Auth::user();
            $param = ['user' => $user];
            return view('index', $param);
        }else{
            return redirect('/login');
        }
    }

    //出勤アクション
    public function workStart()
    {
        //勤務開始時間を記録。「勤務開始」と「休憩終了」の文字がグレーに
        $user = Auth::user();
        $oldattendance = Attendance::where('user_id', $user->id)->latest()->first();

        $oldDay = '';

        //退勤前に2回押せない制御
        if($oldattendance){
            $oldAttendanceStartTime = new Carbon($oldattendance->start_time);
            $oldDay = $oldAttendanceStartTime->startOfday();//Carbonインスタンスを生成することで、starOfdayメソッドが使える
            $today = Carbon::today();

            if(($oldDay == $today) && (empty($oldattendance->end_time))){
                return redirect()->back()->with('message','出勤打刻済みです');
            }
        }

        //退勤後に出勤を押せない制御
        if($oldattendance){
            $oldAttendanceEndTime = new Carbon($oldattendance->end_time);
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

    //退勤アクション
    public function workEnd()
    {
        $user = Auth::user();
        $attendance = Attendance::where('user_id', $user->id)->latest()->first();

        if($attendance){
            if(empty($attendance->end_time)){
                $attendance->update([
                    'end_time' => Carbon::now()
                ]);
                return redirect()->back()->with('message','お疲れ様でした');
            }else{
                $today = new Carbon();
                $day = $today->day;
                $oldAttendanceEndTime = new carbon();
                $oldAttendanceEndTimeDay = $oldAttendanceEndTime->day;
                if($day == $oldAttendanceEndTimeDay){
                    return redirect()->back()->with('massage', '勤務終了済みです');
                }else{
                    return redirect()->back()->with('massage', '勤務開始打刻をしてください');
                }
            }
        }else{
            return redirect()->back()->with('massage', '勤務開始打刻をしてください');
        }
    }

    //休憩開始アクション
    public function restStart()
    {
        $user = Auth::user();//ユーザー認証
        $attendance = Attendance::where('user_id', $user->id)->latest()->first();//attendaceテーブルの最新のレコード1件を取得
        $oldrest = Rest::where('attendance_id', $attendance->id)->first();//attendanceテーブルのidにひもづくrestテーブルのレコードのうち最新の1件を取得

        //休憩開始を連続で押すのを防ぎたい
        if($oldrest->end_time){

        }

        if($attendance->start_time && $oldrest->start_time && !$attendance->end_time){
            //勤務中＆休憩開始データがすでに存在する＆勤務終了していない、ならばstart_timeを更新
            $oldrest->update([
                'start_time' => Carbon::now()
            ]);
            return redirect()->back();
        }elseif($attendance->start_time && !$oldrest->start_time && !$attendance->end_time){
            //勤務中＆休憩開始データが存在しない＆勤務終了していない、ならばレコードを新規作成
            Rest::create([
                'attendance_id' => $attendance->id,
                'start_time' => Carbon::now(),
            ]);
            return redirect()->back();
        }else{
            return redirect()->back();
        }

        //「勤務開始」と「休憩開始」の文字がグレーに
    }
    
    //休憩終了アクション
    public function restEnd()
    {
        $user = Auth::user();
        $attendance = Attendance::where('user_id', $user->id)->latest()->first();
        $oldrest = Rest::where('attendance_id', $attendance->id)->first();

        //休憩終了を連続で押せない制御は？

        //end_timeが存在しない場合は、end_timeを格納
        if($oldrest->start_time && !$oldrest->end_time){
            $oldrest->update([
                'end_time' => Carbon::now(),
            ]);
            return redirect()->back();
        }
        return redirect()->back();
    }

    public function dailyPerformance()
    {
        //「日付一覧」クリックで日付別勤怠ページを表示、日別で勤怠一覧を取得
        // $user = Auth::user();
        //認証すると全ユーザー情報を取得できないのでは？
        $allAttendance = Attendance::all();
        $allRest = Rest::where('attendance_id', $allAttendance->id);

        //まず日付で検索をかける
        

        $today = Carbon::today();
        $dailyAttendance = Attendance::where('date');

        //休憩時間

        //必要な情報はname(users),start_time(attendances),end_time(attendances),start_time(rests),end_time(rests)
        //viewにわたす情報は、
        
    }

}
