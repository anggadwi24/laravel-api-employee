<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\EmployeeController;
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

Route::post('/register', [UsersController::class, 'register']);
Route::post('/login', [AuthController::class, 'index']);
Route::middleware(['auth:sanctum','admin'])->group(function () {
    Route::post('/employee', [EmployeeController::class, 'store']);
    Route::get('/employee', [EmployeeController::class, 'read']);

});

