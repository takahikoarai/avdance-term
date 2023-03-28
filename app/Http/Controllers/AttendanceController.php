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
        $oldAttendance = Attendance::where('user_id', $user->id)->latest()->first();
        $oldDay = '';
        if($oldAttendance){
            $oldAttendanceDay = new Carbon($oldAttendance->date);
            // $oldAttendanceStartTime = new Carbon($oldAttendance->start_time);
            // $oldDay = $oldAttendanceStartTime->startOfday();
            //Carbonインスタンスを生成することで、starOfdayメソッドが使える
        }
        $today = Carbon::today();

        return ($oldAttendanceDay == $today) && ((!$oldAttendance->end_time));
    }

    //「勤務終了」判定
    private function didWorkEnd()
    {
        $user = Auth::user();
        $oldAttendance = Attendance::where('user_id', $user->id)->latest()->first();
        return (isset($oldAttendance->end_time));

        // $oldDay = '';
        
        // if($oldAttendance){
        //     $oldAttendanceEndTime = new Carbon($oldAttendance->end_time);
        //     $oldDay = $oldAttendanceEndTime->startOfDay();
        // }

        // $today = Carbon::today();

        // return ($oldDay == $today);
    }

    //「休憩中」判定
    private function didRestStart()
    {
        $user = Auth::user();
        $oldRest ='';
        $oldDay = '';

        if(Attendance::where('user_id', $user->id)->exists()){
            $attendance = Attendance::where('user_id', $user->id)->latest()->first();

            if(Rest::where('attendance_id', $attendance->id)->exists()){
                $oldRest = Rest::where('attendance_id', $attendance->id)->latest()->first();
            }

            if($oldRest){
                $oldRestStartTime = new Carbon($oldRest->start_time);
                $oldDay = $oldRestStartTime->startOfday();
            }

            $today = Carbon::today();

            //restsテーブルの最新のレコードが今日のデータ、かつ休憩終了がない（レコードがあるということは勤務開始＆休憩開始されている）
            return ($oldDay == $today) && (!$oldRest->end_time);
        }
    }

    public function index()
    {
        //打刻ページを表示
        if(Auth::check()){
            $user = Auth::user();
            $oldAttendance = Attendance::where('user_id', $user->id)->latest()->first();
            $oldDay = new carbon($oldAttendance->date);
            $today = Carbon::today();
            if($oldDay == $today->subDay()){
                if(($oldAttendance->start_time) && (!$oldAttendance->end_time)){
                    $oldAttendance->update([
                        'end_time' => '23:59:59',
                    ]);

                    Attendance::create([
                        'user_id' => $user->id,
                        'date' => Carbon::today(),
                        'start_time' => '0:00:00',
                    ]);
                }
            }

            $isWorkStarted = $this->didWorkStart();
            $isWorkEnded = $this->didWorkEnd();
            $isRestStarted = $this->didRestStart();

            $param = [
                'user' => $user,
                'isWorkStarted' => $isWorkStarted,
                'isWorkEnded' => $isWorkEnded,
                'isRestStarted' => $isRestStarted,
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

        return redirect()->back()->with([
            'user' => $user,
            'isWorkStarted' => $isWorkStarted,
            'isWorkEnded' => $isWorkEnded,
        ]);
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
                return redirect()->back();
            }else{
                $today = new Carbon();
                $day = $today->day;
                $oldAttendanceEndTime = new carbon();
                $oldAttendanceEndTimeDay = $oldAttendanceEndTime->day;
                if($day == $oldAttendanceEndTimeDay){
                    return redirect()->back();
                }else{
                    return redirect()->back();
                }
            }
        }else{
            return redirect()->back();
        }
    }

    //休憩開始アクション
    public function restStart()
    {
        $user = Auth::user();
        $attendance = Attendance::where('user_id', $user->id)->latest()->first();
        
        //「休憩中」判定
        $isRestStarted = $this->didRestStart();

        Rest::create([
            'attendance_id' => $attendance->id,
            'start_time' => Carbon::now(),
        ]);

        return redirect()->back()->with([
            'user' => $user,
            'isRestStarted' => $isRestStarted,
        ]);
    }
    
    //休憩終了アクション
    public function restEnd()
    {
        $user = Auth::user();
        $attendance = Attendance::where('user_id', $user->id)->latest()->first();
        $oldRest = Rest::where('attendance_id', $attendance->id)->latest()->first();

        $isRestStarted = $this->didRestStart();

        //休憩終了を連続で押せない制御は？

        //end_timeが存在しない場合は、end_timeを格納
        if($oldRest->start_time && !$oldRest->end_time){
            $oldRest->update([
                'end_time' => Carbon::now(),
            ]);
        }

        return redirect()->back()->with([
            'user' => $user,
            'isRestStarted' => $isRestStarted,
        ]);
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
