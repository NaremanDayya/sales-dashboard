<?php

use App\Http\Controllers\Admin\SalesRepController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth',])->group(function () {
    Route::resource('sales-reps', SalesRepController::class);
});
Route::post('/sales-reps/export', [SalesRepController::class, 'export'])->name('sales-reps.export');
Route::get('sales-reps/{salesRep}/generate-report', [SalesRepController::class, 'generateReport'])->name('sales-reps.generate-report');
Route::get('sales-reps/{salesRep}/download-report', [SalesRepController::class, 'downloadPdf'])->name('sales-reps.download-report');
