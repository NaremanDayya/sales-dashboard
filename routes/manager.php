<?php

use App\Http\Controllers\AdminImpersonationController;
use App\Http\Controllers\ManagerAssignmentController;
use App\Http\Controllers\ManagerClientChatController;
use App\Http\Controllers\ManagerDashboardController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    
    Route::prefix('manager')->name('manager.')->group(function () {
        Route::get('/dashboard', [ManagerDashboardController::class, 'index'])->name('dashboard');
        Route::get('/team-member/{teamMember}', [ManagerDashboardController::class, 'teamMemberDetails'])->name('team-member.details');
        Route::get('/team/clients', [ManagerDashboardController::class, 'teamClients'])->name('team.clients');
        Route::get('/team/agreements', [ManagerDashboardController::class, 'teamAgreements'])->name('team.agreements');
        
        Route::get('/chats', [ManagerClientChatController::class, 'index'])->name('chats.index');
        Route::get('/chats/{chat}', [ManagerClientChatController::class, 'show'])->name('chats.show');
        Route::post('/chats/client/{client}', [ManagerClientChatController::class, 'store'])->name('chats.store');
        Route::post('/chats/{chat}/message', [ManagerClientChatController::class, 'sendMessage'])->name('chats.send-message');
        
        Route::post('/assign/{salesRep}', [ManagerAssignmentController::class, 'assign'])->name('assign');
        Route::delete('/remove/{salesRep}', [ManagerAssignmentController::class, 'remove'])->name('remove');
        Route::get('/available-managers/{salesRep}', [ManagerAssignmentController::class, 'availableManagers'])->name('available-managers');
    });
    
    Route::prefix('admin')->name('admin.')->middleware('can:viewAny,App\Models\User')->group(function () {
        Route::post('/impersonate/manager/{manager}', [AdminImpersonationController::class, 'start'])->name('impersonation.start');
        Route::post('/impersonate/stop', [AdminImpersonationController::class, 'stop'])->name('impersonation.stop');
    });
});
