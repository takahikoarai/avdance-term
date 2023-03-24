<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\LoginController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/', function () {
//     return view('attendance');//これをindexに変更する
// })->name('login');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

require __DIR__.'/auth.php';


Route::get('/login', [LoginController::class, 'getIndex'])->name('login');
Route::post('/login', [LoginController::class, 'postIndex'])->name('login');

Route::get('/', [AttendanceController::class, 'index'])->middleware('auth');

Route::get('/attendance',[AttendanceController::class, 'dailyPerformance']);

Route::post('/workStart', [AttendanceController::class, 'workStart']);
Route::post('/workEnd', [AttendanceController::class, 'workEnd']);
Route::post('/restStart', [AttendanceController::class, 'restStart']);
Route::post('/restEnd', [AttendanceController::class, 'restEnd']);