<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('login', [\App\Http\Controllers\Auth\AuthController::class,'login']);
Route::post('forgot_password', [\App\Http\Controllers\Auth\AuthController::class,'forgot_password']);
Route::post('reset_password', [\App\Http\Controllers\Auth\AuthController::class,'reset_password']);
Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::patch('update_profile/{user_id}', [\App\Http\Controllers\Auth\AuthController::class,'update_profile']);
    Route::patch('update_password/{user_id}', [\App\Http\Controllers\Auth\AuthController::class,'update_password']);
    Route::get('profile/{id}', [\App\Http\Controllers\Auth\AuthController::class,'get_user_profile']);
    Route::post('logout', [\App\Http\Controllers\Auth\AuthController::class, 'logout']);
    Route::get('get-departments', [\App\Http\Controllers\EquipmentsController::class, 'get_departments']);
    Route::get('get-equipments', [\App\Http\Controllers\EquipmentsController::class, 'get_equipments']);
    Route::post('save-new-equipment', [\App\Http\Controllers\EquipmentsController::class, 'save_new_equipment']);
    Route::patch('update-equipment/{equipment}', [\App\Http\Controllers\EquipmentsController::class, 'update_equipment']);
    Route::get('download-qrcode/{equipment}', [\App\Http\Controllers\EquipmentsController::class, 'download_qrcode']);
    //Departments
    Route::post('save-new-department', [\App\Http\Controllers\DepartmentController::class, 'save_new_department']);
    Route::patch('update-department/{department}', [\App\Http\Controllers\DepartmentController::class, 'update_department']);

    //Dashboard
    Route::get('load-dashboard', [\App\Http\Controllers\DashboardController::class, 'load_dashboard']);

    //Schedule Routes
    Route::get('get-scheduled-maintenance', [\App\Http\Controllers\ProcessController::class, 'get_scheduled_maintenance']);
    Route::post('save-report/{scheduled}', [\App\Http\Controllers\ProcessController::class, 'save_report']);
});

//Schedule Routes
Route::get('check-schedule-maintenance', [\App\Http\Controllers\ProcessController::class, 'check_schedule_maintenance']);
// Fault report
Route::post('send_fault_report/{qr_token}', [\App\Http\Controllers\ProcessController::class, 'send_fault_report']);
Route::get('get-fault-reports', [\App\Http\Controllers\ProcessController::class, 'get_falt_reports']);
Route::post('save-reported_fault/{fault_reported}', [\App\Http\Controllers\ProcessController::class, 'save_reported_fault']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
