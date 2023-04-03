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
        $oldDay = '';
        
        if($oldAttendance){
            $oldDay = new Carbon($oldAttendance->date);
        }

        $today = Carbon::today();

        return ($oldDay == $today);
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
    private function actualWorkTime($attendanceToday, $restToday)
    {
        //勤務時間の算出
        $attendanceStartTime = $attendanceToday->start_time;
        $attendanceStartTimeCarbon = new Carbon($attendanceToday->start_time);
        $attendanceEndTime = $attendanceToday->end_time;
        $attendanceEndTimeCarbon = new Carbon($attendanceToday->end_time);
        $workTimeDiffInSeconds = $attendanceEndTimeCarbon->diffInSeconds($attendanceStartTimeCarbon);
        $workTimeSeconds = floor($workTimeDiffInSeconds % 60);
        $workTimeMinutes = floor($workTimeDiffInSeconds / 60);
        $workTimeHours = floor($workTimeMinutes / 60);
        $workTime = $workTimeHours.":".$workTimeMinutes.":".$workTimeSeconds;

        //休憩時間の算出
        $restStartTime = new Carbon($restToday->start_time);
        $restEndTime = new Carbon($restToday->end_time);
        $restTimeDiffInSeconds = $restEndTime->diffInSeconds($restStartTime);
        $restTimeSeconds = floor($restTimeDiffInSeconds % 60);
        $restTimeMinutes = floor($restTimeDiffInSeconds / 60);
        $restTimeHours = floor($restTimeMinutes / 60);
        $restTime = $restTimeHours.":".$restTimeMinutes.":".$restTimeSeconds;

        //実労働時間の算出
        $actualWorkTimeDiffInSeconds = $workTimeDiffInSeconds - $restTimeDiffInSeconds;
        $actualWorkTimeSeconds = floor($actualWorkTimeDiffInSeconds % 60);
        $actualWorkTimeMinutes = floor($actualWorkTimeDiffInSeconds / 60);
        $actualTimeHours = floor($actualWorkTimeMinutes / 60);
        $actualWorkTime = $actualTimeHours.":".$actualWorkTimeMinutes.":".$actualWorkTimeSeconds;
        
        $userId = User::where('id', $attendanceToday->user_id)->first();
        $userName = $userId->name;

        $param = [
            'userName' => $userName,
            'attendanceStartTime' => $attendanceStartTime,
            'attendanceEndTime' =>$attendanceEndTime,
            'restTime' => $restTime,
            'actualWorkTime' => $actualWorkTime,
        ];

        return $param;
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
        $today = Carbon::today();
        $resultArray[] = array();

        $attendanceTodayAll = Attendance::where('date', $today)->get();
        foreach($attendanceTodayAll as $attendanceToday){

            $restTodayAll = Rest::where('attendance_id', $attendanceToday->id)->get();

            foreach($restTodayAll as $restToday){
                $result = $this->actualWorkTime($attendanceToday, $restToday);//ユーザー名、勤務開始時間、勤務終了時間、休憩時間$paramを受け取った
                array_push($resultArray, $result);
            }
        }

        return view('/attendance')->with([
            'resultArray' => $resultArray,
        ]);

    }

    public function dailyPerformanceSubDay()
    {

    }

    public function dailyPerformanceAddDay()
    {
        
    }

}
