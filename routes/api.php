<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserRegistrationController;
use App\Http\Controllers\Api\UserDataFetchController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::post('/user-registration/{apiKey}', [UserRegistrationController::class, 'register']);
Route::get('/user-data/{apiKey}', [UserDataFetchController::class, 'fetch']);
