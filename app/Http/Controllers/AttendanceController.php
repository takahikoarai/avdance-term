<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Attendance;
use App\Models\Rest;
use Carbon\Carbon;
use App\Models\User;

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

    //勤務時間-休憩時間の計算
    private function actualWorkTime()
    {

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

    public function dailyPerformanceToday()
    {
        // $user = User::where('name')->latest()->first();
        $today = Carbon::today();

        //休憩時間と勤務時間を算出する計算
        $attendanceToday = Attendance::where('date', $today)->latest()->first();

        $attendanceStartTime = new Carbon($attendanceToday->start_time);
        $attendanceEndTime = new Carbon($attendanceToday->end_time);
        $workTimeInt = $attendanceEndTime->diffInSeconds($attendanceStartTime);//int(15)
        

        //勤務時間
        $attendanceStartTime = $attendanceToday->start_time;//string(8) "00:16:24"
        $attendanceStartTime = strtotime($attendanceStartTime);//int(1680189384)
        $attendanceEndTime = $attendanceToday->end_time;//string(8) "00:16:39"
        $attendanceEndTime = strtotime($attendanceEndTime);//int(1680189399)
        $workTime = $attendanceEndTime - $attendanceStartTime;//int(15)


        // $param = [
        //     'AttendanceToday' => $AttendanceToday,
        //     'workTime' => $workTime,
        // ];

        return view('attendance')->with([
            'attendanceToday' => $attendanceToday,
            'workTime' => $workTime,
        ]);

        // $dailyRestToday = Rest::where('attendance_id', $dailyAttendanceToday->id);
        



        //attendanceレコードを持つユーザーを取得
        // $dailyPerformance = User::has('attendances')
        //     //そのうち日付が今日のレコード
        //     ->join('attendances', 'attendances.date', '=', $today)
        //     ->join('rests', 'rests.attendance_id', '=', 'attendances.id')
        //     ->orderBy('user.id', 'desc')
        //     ->get();
        // var_dump($dailyPerformance);



        // //今日の勤怠記録を取得
        // $dailyAttendanceToday = Attendance::where('date', $today);
        // //今日の
        // $dailyRestToday = Rest::where('attendance_id', $allAttendance->id);

        
        
        //休憩時間の計算

        //必要な情報はname(users),start_time(attendances),end_time(attendances),start_time(rests),end_time(rests)
        //viewにわたす情報は、

    }

    public function dailyPerformanceSubDay()
    {

    }

    public function dailyPerformanceAddDay()
    {
        
    }

}
