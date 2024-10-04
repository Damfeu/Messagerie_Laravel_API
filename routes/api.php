<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\GroupController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {

    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::post('/groups/{groupId}/members', [GroupController::class, 'addMember']);
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/user/groups', [GroupController::class, 'getUserGroups']);
});


Route::post('/groups', [GroupController::class, 'createGroup']);
Route::get('/getGroups', [GroupController::class, 'getGroups']);

Route::post('/groups/{groupId}/files', [GroupController::class, 'uploadFile']);
Route::post('/verify-code', [AuthController::class, 'verifyCode']);
Route::get('/groups', [GroupController::class, 'index']);
Route::get('/groups/{groupId}/files', [GroupController::class, 'listFilesByGroup']);
Route::get('/users', [AuthController::class, 'listUsers']);