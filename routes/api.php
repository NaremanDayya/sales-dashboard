<?php

use App\Http\Controllers\ClientController;
use App\Http\Controllers\NotificationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
Route::get('/clients', [ClientController::class, 'chatClients']);
Route::get('/notifications', [NotificationController::class, 'getNotifications']);
