<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminCompanyController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/admin/dashboard', [AdminCompanyController::class, 'index'])->name('admin.dashboard');

Route::post('/admin/company/{id}/status', [AdminCompanyController::class, 'updateStatusWeb'])->name('admin.company.status');