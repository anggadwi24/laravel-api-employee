<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\BalanceController;
use App\Http\Controllers\API\BalanceEmployeeController;
use App\Http\Controllers\API\EmployeeController;
use App\Http\Controllers\API\ProfileController;
use App\Http\Controllers\API\UsersController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/login', [AuthController::class, 'index']);
Route::middleware(['auth:sanctum','admin'])->group(function () {
    Route::post('/employee', [EmployeeController::class, 'store']);
    Route::get('/employee', [EmployeeController::class, 'show']);
    Route::get('/employee/dropdown', [EmployeeController::class, 'dropdown']);

    Route::put('/employee/{email}', [EmployeeController::class, 'update']);
    Route::get('/employee/{email}', [EmployeeController::class, 'find']);
    Route::delete('/employee/{email}', [EmployeeController::class, 'destroy']);

    Route::post('/users', [UsersController::class, 'store']);
    Route::get('/users', [UsersController::class, 'show']);
    Route::get('/users/dropdown', [UsersController::class, 'dropdown']);
    Route::get('/users/{email}', [UsersController::class, 'find']);
    Route::put('/users/{email}', [UsersController::class, 'update']);
    Route::delete('/users/{email}', [UsersController::class, 'destroy']);

    Route::get('/balance', [BalanceController::class, 'show']);
    Route::get('/balance/employee/{email}', [BalanceController::class, 'findByEmail']);
    Route::get('/balance/{id}', [BalanceController::class, 'find']);
    Route::post('/balance/in/{email}', [BalanceController::class, 'in']);
    Route::post('/balance/out/{email}', [BalanceController::class, 'out']);

    Route::post('/balance/submission/{id}/{status}', [BalanceController::class, 'submission']);


});

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/my', function (Request $request) {
        return $request->user();
    });
    Route::post('/submission', [BalanceEmployeeController::class, 'submission']);
    Route::get('/history', [BalanceEmployeeController::class, 'history']);
    Route::post('/biodata', [ProfileController::class, 'biodata']);

    Route::get('/profile', [ProfileController::class, 'show']);
    Route::post('/profile', [ProfileController::class, 'update']);
    Route::get('/logout', [AuthController::class, 'logout']);




});

