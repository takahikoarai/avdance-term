<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Attendance;
use App\Models\Rest;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    //「勤務開始」判定
    private function didWorkStart()
    {
        $user = Auth::user();
        $oldattendance = Attendance::where('user_id', $user->id)->latest()->first();
        $oldDay = '';
        if($oldattendance){
            $oldAttendanceStartTime = new Carbon($oldattendance->start_time);
            $oldDay = $oldAttendanceStartTime->startOfday();//Carbonインスタンスを生成することで、starOfdayメソッドが使える
        }
        $today = Carbon::today();

        return ($oldDay == $today) && (empty($oldattendance->end_time));
    }

    //「勤務終了」判定
    private function didWorkEnd()
    {
        $user = Auth::user();
        $oldattendance = Attendance::where('user_id', $user->id)->latest()->first();
        $oldDay = '';
        
        if($oldattendance){
            $oldAttendanceEndTime = new Carbon($oldattendance->end_time);
            $oldDay = $oldAttendanceEndTime->startofDay();
        }

        $today = Carbon::today();

        return ($oldDay == $today);
    }

    public function index()
    {
        //打刻ページを表示
        if(Auth::check()){
            $isWorkStarted = $this->didWorkStart();
            $isWorkEnded = $this->didWorkEnd();
            $user = Auth::user();
            $param = [
                'user' => $user,
                'isWorkStarted' => $isWorkStarted,
                'isWorkEnded' => $isWorkEnded,
            ];
            return view('/index', $param);
        }else{
            return redirect('/login');
        }
    }

    //出勤アクション
    public function workStart()
    {
        $user = Auth::user();

        //「勤務開始」判定
        $isWorkStarted = $this->didWorkStart();

        //「勤務終了」判定
        $isWorkEnded = $this->didWorkEnd();

        Attendance::create([
            'user_id' => $user->id,
            'date' => Carbon::today(),
            'start_time' => Carbon::now(),
        ]);

        $param = [
            'isWorkStarted' => $isWorkStarted,
            'isWorkEnded' => $isWorkEnded,
        ];
        return view('/', $param);
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
                    return redirect()->back()->with('message', '勤務終了済みです');
                }else{
                    return redirect()->back()->with('message', '勤務開始打刻をしてください');
                }
            }
        }else{
            return redirect()->back()->with('message', '勤務開始打刻をしてください');
        }
    }

    //休憩開始アクション
    public function restStart(Request $request)
    {
        $user = Auth::user();//ユーザー認証
        $attendance = Attendance::where('user_id', $user->id)->latest()->first();//attendaceテーブルの最新のレコード1件を取得

        if(!$attendance){
            return redirect()->back()->with('message', '勤務開始打刻をしてください');
        }else{
            $today = new Carbon();
            $day = $today->day;
            $oldAttendanceEndTime = new carbon();
            $oldAttendanceEndTimeDay = $oldAttendanceEndTime->day;
            if($day == $oldAttendanceEndTimeDay){
                return redirect()->back()->with('message', '勤務終了済みです');
            }
        }    

        $oldrest = Rest::where('attendance_id', $attendance->id)->first();//attendanceテーブルのidにひもづくrestテーブルのレコードのうち最新の1件を取得

        //休憩開始を連続で押すのを防ぎたい
        $request->session()->regenerateToken();

        if($oldrest){
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
            }
        }else{
            Rest::create([
                'attendance_id' => $attendance->id,
                'start_time' => Carbon::now(),
            ]);
            return redirect()->back();
        }

        //「勤務開始」と「休憩開始」の文字がグレーに
    }
    
    //休憩終了アクション
    public function restEnd()
    {
        $user = Auth::user();
        $attendance = Attendance::where('user_id', $user->id)->latest()->first();

        if(!$attendance){
            return redirect()->back()->with('message', '勤務開始打刻をしてください');
        }else{
            $today = new Carbon();
            $day = $today->day;
            $oldAttendanceEndTime = new carbon();
            $oldAttendanceEndTimeDay = $oldAttendanceEndTime->day;
            if($day == $oldAttendanceEndTimeDay){
                return redirect()->back()->with('message', '勤務終了済みです');
            }
        }

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
