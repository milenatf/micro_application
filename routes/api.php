<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Register\RegisterController;
use App\Http\Controllers\Api\Teacher\TeacherController;
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

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [RegisterController::class, 'store']);

Route::middleware(['microAuthAutenticate'])->group(function(){
    // Route's teacher
    Route::get('teachers', [TeacherController::class, 'show']);
    Route::put('teachers', [TeacherController::class, 'update']);
    Route::delete('teachers', [TeacherController::class, 'delete']);

    Route::get('me', [AuthController::class, 'me']);
    Route::get('logout', [AuthController::class, 'logout']);

    Route::get('/dash', function() {
        return response()->json('Acessou o dashboard');
    });
});

Route::get('/', function() {
    return response()->json(['status' => 'success', 'message' => 'API applicação OK']);
});
