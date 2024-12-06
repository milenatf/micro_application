<?php

use App\Http\Controllers\Api\Auth\AuthController;
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

Route::middleware(['microAuthAutenticate'])->group(function(){
    Route::get('me', [AuthController::class, 'me']);

    Route::get('/dash', function() {
        return response()->json('Acessou o dashboard');
    });

    Route::get('teacher/{uuid}', [TeacherController::class, 'show']);
    // Route::post('teacher', [TeacherController::class, 'store']);
});

Route::get('/', function() {
    return response()->json(['status' => 'success', 'message' => 'API applicação OK']);
});
