<?php

use App\Http\Controllers\Auth\AuthenticationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
Route :: get('/welcome',function(){
    return 'welcom to API';
});
Route :: get('/test',function(){
    return response([
        'message' => 'this is test API',],200);
});
Route::post('/register/job-seeker', [AuthenticationController::class, 'registerJobSeeker']);
Route::post('/register/company', [AuthenticationController::class, 'registerCompany']);
