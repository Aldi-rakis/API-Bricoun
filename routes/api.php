<?php

use App\Http\Controllers\Api\AnswerController;
use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\Api\LogoutController;
use App\Http\Controllers\Api\QuestionnaireController;
use App\Http\Controllers\Api\RegisterController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');



Route::post('/login', LoginController::class);

Route::post('/register', RegisterController::class);
Route::post('/logout', logoutController::class)->middleware('auth:sanctum');

// get data question with  Sanctum
Route::get('/question', [QuestionnaireController::class, 'index']);
Route::post('/question', [QuestionnaireController::class, 'store']);


Route::post('/answer', [AnswerController::class, 'store'])->middleware('auth:sanctum');
Route::get('/answer', [AnswerController::class, 'index'])->middleware('auth:sanctum');

Route::get('allAnswersByUser', [AnswerController::class, 'getAllAnswersByUser']); // (/getAllAnswersByUser)
Route::get('answer/{id}', [AnswerController::class, 'show']);


Route::get('updatestatusUSer', [UserController::class, 'updateStatusForAllUsers']);