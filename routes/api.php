<?php

use App\Http\Controllers\Auth\AuthenticationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminCompanyController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\Api\JobSeekerProfileController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/welcome',function(){
    return 'welcom to API';
});

Route::get('/test',function(){
    return response([
        'message' => 'this is test API',],200);
});

Route::post('/register/job-seeker', [AuthenticationController::class, 'registerJobSeeker']);
Route::post('/register/company', [AuthenticationController::class, 'registerCompany']);
Route::post('/login', [AuthenticationController::class, 'login']);

Route::get('/test-token', function () {
    $user = \App\Models\User::first();
    return $user->createToken('test')->plainTextToken;
});

Route::post('/admin/company/{id}/status', [AdminCompanyController::class, 'updateStatus']);

Route::get('/admin/companies/pending', [AdminCompanyController::class, 'getPendingCompanies']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/company/dashboard', [CompanyController::class, 'dashboard']);
    Route::get('/company/all-jobs', [CompanyController::class, 'allJobs']);
    Route::post('/jobs', [JobController::class, 'store']);
    
   Route::put('/jobs/{id}', [JobController::class, 'update']);
    Route::delete('/jobs/{id}', [JobController::class, 'destroy']);
    Route::post('/jobs/{id}/close', [JobController::class, 'closeJob']);
});
Route::middleware('auth:sanctum')->group(function () {
    
    Route::get('/company/profile', [CompanyController::class, 'getProfile']);
    
    Route::put('/company/update', [CompanyController::class, 'updateProfile']);
    
    Route::post('/company/update-photo', [CompanyController::class, 'updatePhoto']);
    
});
Route::get('/jobs', [JobController::class, 'index']);
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/jobs', [JobController::class, 'index']);
 Route::post('/jobs/{job}/apply', [JobController::class, 'apply']);

});
Route::post('/jobs/{id}/apply', [JobController::class, 'apply']);
Route::middleware('auth:sanctum')->group(function () {
  
  
    Route::post('/logout', [\App\Http\Controllers\Auth\AuthenticationController::class, 'logout']);
});

Route::get('/user/saved-jobs', [JobController::class, 'getSavedJobs']);
Route::middleware('auth:sanctum')->group(function () {

    Route::get('/saved-jobs', [JobController::class, 'getSavedJobs']);
    Route::post('/jobs/{id}/save', [JobController::class, 'toggleSave']);
    
});
Route::get('/jobs/{id}/applications', [JobController::class, 'getApplications']);
Route::middleware('auth:sanctum')->group(function () {
   
    Route::get('/applications', [JobController::class, 'myApplications']); 
});
Route::middleware('auth:sanctum')->group(function () {

    Route::get('/company/applicants', [CompanyController::class, 'getAllApplicants']);

Route::post('/applications/{id}/status', [CompanyController::class, 'updateApplicationStatus']);
});
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/company/employees', [CompanyController::class, 'getEmployees']);
});

Route::middleware('auth:sanctum')->group(function () {
    

    Route::get('/jobseeker/profile', [JobSeekerProfileController::class, 'getProfile']);
    Route::put('/jobseeker/update', [JobSeekerProfileController::class, 'updateProfile']);
    Route::post('/jobseeker/update-photo', [JobSeekerProfileController::class, 'updatePhoto']);
    
});
Route::middleware('auth:sanctum')->post('/change-password', [AuthenticationController::class, 'changePassword']);
Route::post('/verify-forget-password', [AuthenticationController::class, 'verifyForgetPassword']);
Route::post('/reset-password', [AuthenticationController::class, 'resetPassword']);