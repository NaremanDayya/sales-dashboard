<?php

use App\Http\Controllers\Admin\AgreementEditRequestController as AdminAgreementEditRequestController;
use App\Http\Controllers\AgreementController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ClientEditRequestController;
use App\Http\Controllers\Admin\ClientRequestController;
use App\Http\Controllers\Admin\ClientEditRequestController as AdminClientEditRequestController;
use App\Http\Controllers\Admin\SalesRepController;
use App\Http\Controllers\AdminCommissionController;
use App\Http\Controllers\AgreementEditRequestController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\SalesRepCommissionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PdfController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\TargetController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Livewire\Chat;
use App\Livewire\CustomChat;
use App\Livewire\Index;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Spatie\Browsershot\Browsershot;
use Spatie\Permission\Middleware\RoleMiddleware;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Carbon;
use App\Models\SalesRep;
use App\Models\Service;
use App\Models\Target;
use App\Http\Controllers\SalesRepLoginIpController;

require __DIR__ . '/auth.php';
require __DIR__ . '/salesRep.php';

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('admin/settings')->middleware('auth')->group(function () {
    Route::get('/', [App\Http\Controllers\Admin\SettingController::class, 'index'])->name('settings.index');
    Route::post('/', [App\Http\Controllers\Admin\SettingController::class, 'update'])->name('settings.update');
    Route::post('/settings/commission-threshold', [App\Http\Controllers\Admin\SettingController::class, 'updateCommissionThreshold'])->name('settings.updateCommissionThreshold');

});
Route::get('/kernel-test', function() {
    $kernel = app()->make(\App\Console\Kernel::class);
    return response()->json(['status' => 'Kernel loaded successfully']);
});
Route::middleware(['auth', \App\Http\Middleware\AuthorizeSalesRep::class])->group(callback: function () {
    Route::resource('sales-reps.clients', ClientController::class)->except(['edit', 'update']);
});
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});
Route::middleware('auth')->group(function () {
    Route::get('/showProfile', [ProfileController::class, 'show'])->name('profile.show.custom');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
Route::post('/admin/update-photo', [ProfileController::class, 'updatePhoto'])->name('admin.updatePhoto');

});

Route::prefix('sales-reps')->group(function () {
    Route::get('{id}/export-pdf', [pdfController::class, 'exportSalesRepPdf'])->name('salesreps.export.pdf');
    Route::get('{id}/p4review-pdf', [pdfController::class, 'previewSalesRepPdf'])->name('salesreps.preview.pdf');
});
Route::get('/admin/impersonate/stop', [SalesRepController::class, 'stopImpersonate'])
    ->middleware('auth');
/*
|--------------------------------------------------------------------------
| Core Features: Clients, Services, Agreements, Targets
|--------------------------------------------------------------------------
*/
Route::middleware([
    'auth',
    App\Http\Middleware\AdminRoleMiddleware::class
])->group(function () {
    Route::get('/salesreps/credentials', function () {
        $csvPath = 'exports/sales_reps_credentials.csv';

        if (!Storage::exists($csvPath)) {
            return redirect()->back()->with('error', 'No credentials data available yet');
        }

        $credentials = [];
        $file = Storage::get($csvPath);
        // Explode by line, handle both Unix and Windows line endings
        $lines = preg_split('/\r\n|\r|\n/', $file);

        // Skip header row and process data
        for ($i = 1; $i < count($lines); $i++) {
            $line = trim($lines[$i]);
            if (!empty($line)) {
                $credentials[] = str_getcsv($line);
            }
        }

        return view('salesRep.credentials', compact('credentials'));
    })->name('salesreps.credentials');
Route::get('/admin/shared-companies', [ClientController::class, 'sharedCompanies'])->name('admin.shared-companies');

    Route::get('/admin/impersonate/{salesRep}', [SalesRepController::class, 'impersonate'])
        ->middleware(['auth']); // admin middleware


Route::put('/admin/commissions/{commission}/update-type', [AdminCommissionController::class, 'updateCommissionType'])
    ->name('admin.commissions.updateType');
Route::put('/admin/password/update',[AuthenticatedSessionController::class, 'updatePassword'])
    ->name('admin.password.update');
});
Route::prefix('admin/sales-rep-ips')->group(function () {
 Route::get('/', [SalesRepLoginIpController::class, 'index'])->name('admin.sales-rep-ips.index');
    Route::post('block/{ip}', [SalesRepLoginIpController::class, 'block'])->name('admin.sales-rep-ips.block');
    Route::post('unblock/{ip}', [SalesRepLoginIpController::class, 'unblock'])->name('admin.sales-rep-ips.unblock');
    Route::post('add-temp-ip/{salesRep}', [SalesRepLoginIpController::class, 'addTemporaryIp'])->name('admin.sales-rep-ips.add-temp-ip');
});

Route::get('/run-scheduled-tasks', function () {

    if (request('key') !== env('CRON_SECRET_KEY')) {
        abort(403, 'Unauthorized');
    }


    Artisan::call('notify:agreement-notice');

    Log::info('Running notify:pended-request');
    Artisan::call('notify:late-customers');
    Log::info('Running notify:pended-request');

    Artisan::call('notify:pended-request');


    Log::info('Scheduled tasks triggered via route at: ' . now());

    return 'Scheduled tasks executed at ' . now();
});
Route::get('/generate-monthly-targets', function () {

    if (request('key') !== config('app.cron_secret_key')) {
        abort(403, 'Unauthorized');
    }


    Artisan::call('targets:generate-monthly');

    Log::info('Scheduled target tasks triggered via route at: ' . now());

    return 'Scheduled tasks executed at ' . now();
});
Route::middleware('auth', \App\Http\Middleware\AuthorizeSalesRep::class)->group(function () {
    Route::post('/agreements/export', [AgreementController::class, 'export'])->name('agreements.export');
    Route::resource('sales-rep.targets', TargetController::class);
    Route::resource('salesrep.agreements', AgreementController::class);
    Route::get('targets', [TargetController::class, 'allTargets'])->name('allTargets');
    Route::get('clients', [ClientController::class, 'allClients'])->name('allClients');
    Route::get('agreements', [AgreementController::class, 'allAgreements'])->name('allAgreements');

    Route::put('sales-reps/{salesrep}/agreements/{agreement}/notice-status', [AgreementController::class, 'updateNoticeStatus'])
        ->name('agreements.updateNoticeStatus');
});
Route::middleware(['auth'])->group(function () {
    Route::get('/clients/{client}/edit', [ClientController::class, 'edit'])->name('clients.edit');
    Route::put('/clients/{client}', [ClientController::class, 'update'])->name('clients.update');
    Route::resource('services', ServiceController::class);
    Route::put('/clients/{client}/update-last-contact', [ClientController::class, 'updateLastContact'])
        ->name('clients.update-last-contact');
Route::get('/company-name-suggestions', [ClientController::class, 'suggestCompanyNames'])->name('clients.suggest-company-names');

});
Route::get('/sales-reps/{salesRep}/pdf', [PdfController::class, 'downloadSalesRepPdf'])
    ->name('sales-reps.pdf.download');

Route::get('/sales-reps/{salesRep}/pdf-preview', [PdfController::class, 'previewSalesRepPdf'])
    ->name('sales-reps.pdf.preview');
Route::prefix('pdf')->group(function () {
    Route::get('/clients/{client}/profile', [PdfController::class, 'clientProfile'])->name('pdf.client.profile');
    Route::get('/clients/{client}/agreements', [PdfController::class, 'clientAgreements'])->name('pdf.client.agreements');
    Route::get('/clients/directory', [PdfController::class, 'clientDirectory'])->name('pdf.client.directory');
    Route::get('/clients/status-report', [PdfController::class, 'clientStatusReport'])->name('pdf.client.status-report');
    // Preview routes
    Route::get('/clients/{client}/profile/preview', [PdfController::class, 'previewClientProfile'])->name('pdf.client.profile.preview');
    Route::get('/clients/{client}/agreements/preview', [PdfController::class, 'previewClientAgreements'])->name('pdf.client.agreements.preview');
    Route::get('/clients/directory/preview', [PdfController::class, 'previewClientDirectory'])->name('pdf.client.directory.preview');
    Route::get('/clients/status-report/preview', [PdfController::class, 'previewClientStatusReport'])->name('pdf.client.status-report.preview');
});

/*
|--------------------------------------------------------------------------
| Client Edit Requests (Sales Rep Side)
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    Route::get('/client-requests', [ClientEditRequestController::class, 'index'])->name('client-request.index');
});
 Route::prefix('admin')->group(function() {
    Route::post('/sales-reps/bulk-activate', [SalesRepController::class, 'bulkActivate'])
        ->name('sales-reps.bulk-activate');

    Route::post('/sales-reps/bulk-deactivate', [SalesRepController::class, 'bulkDeactivate'])
        ->name('sales-reps.bulk-deactivate');
});
 Route::put('/sales-reps/{salesrep}/update-password', [SalesRepController::class, 'updatePassword'])
        ->name('salesrep.password.change');
Route::get('/commissions/{commission}/payment-done', [SalesRepCommissionController::class, 'changePaymentStatus'])
        ->name('paymentDone');
Route::get('/client-edit-requests/create/{client}', [ClientEditRequestController::class, 'create'])->name('client-edit-requests.create');
Route::post('/clients/{client}/client-edit-requests', [ClientEditRequestController::class, 'store'])->name('client-edit-requests.store');
Route::get('/sales-reps/clients/{client}/edit-requests/{client_request}', [ClientEditRequestController::class, 'show'])->name('sales-reps.client-requests.show');
Route::get('/sales-reps/clients/{client}/requests/{client_request}', [ClientEditRequestController::class, 'showRequest'])->name('sales-reps.clientRequests.show');
Route::get('/sales-reps/agreements/{agreement}/edit-requests/{agreement_request}', [AgreementEditRequestController::class, 'show'])->name('sales-reps.agreement-requests.show');
Route::post('/client/{client}/client-requests', [ClientController::class, 'storeRequest'])->name('client-requests.store');

Route::middleware('auth')->group(function () {
    Route::get('/clients/export', [ClientController::class, 'export'])->name('clients.export');
});

/*
|--------------------------------------------------------------------------
| Admin - Agreement Edit Requests
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/admin/agreement-edit-requests', [AdminAgreementEditRequestController::class, 'index'])->name('admin.agreement-edit-requests.index');
});

Route::get('/admin/agreement-edit-requests/{agreement}/edit/{agreement_request}', [AdminAgreementEditRequestController::class, 'edit'])->name('admin.agreement-request.edit');
Route::get('admin/allRequests', [SalesRepController::class, 'allRequests'])->name('admin.allRequests');
Route::get('salesrep/{salesRep}/MyRequests', [SalesRepController::class, 'myRequests'])->name('myRequests');
Route::put(
    'admin/agreement-edit-requests/{agreement}/update/{agreement_request}',
    [AdminAgreementEditRequestController::class, 'update']
)->name('admin.agreement-request.update');
Route::delete(
    'admin/agreement-edit-requests/{agreement}/delete/{agreement_request}',
    [AdminAgreementEditRequestController::class, 'destroy']
)->name('admin.agreement-request.destroy');
Route::get(
    'admin/agreement-edit-requests/{agreement}/review/{agreement_request}',
    [AdminAgreementEditRequestController::class, 'review']
)->name('admin.agreement-request.review');
Route::get('/admin/agreement-pended-edit-requests', [AdminAgreementEditRequestController::class, 'pendedRequests'])->name('admin.agreement-edit-requests.pended');

Route::get('/agreement-edit-requests/{agreementEditRequest}', [AgreementEditRequestController::class, 'show'])
    ->name('agreement-edit-requests.show')
    ->middleware('auth');

Route::get('/admin/agreement-edit-requests/{agreementEditRequest}', [AdminAgreementEditRequestController::class, 'show'])
    ->name('admin.agreement-edit-requests.show')
    ->middleware(['auth']);

Route::middleware('auth')->group(function () {
    Route::get('/agreement-requests', [AgreementEditRequestController::class, 'index'])->name('agreement-request.index');
});

Route::get('/agreement-edit-requests/create/{agreement}', [AgreementEditRequestController::class, 'create'])->name('agreement-edit-requests.create');

Route::post('/agreements/{agreement}/agreement-edit-requests', [AgreementEditRequestController::class, 'store'])->name('agreement-edit-requests.store');

Route::get('/sales-reps/agreements/{agreement}/edit-requests/{agreement_request}', [AgreementEditRequestController::class, 'show'])->name('sales-reps.agreement-requests.show');

/*
|--------------------------------------------------------------------------
| Admin - Client Edit Requests
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    Route::get('/admin/client-edit-requests', [AdminClientEditRequestController::class, 'index'])->name('admin.client-edit-requests.index');
    Route::get('/agreements/{agreement}/pdf/preview', [PdfController::class, 'previewAgreementPdf'])
        ->name('agreements.pdf.preview');

    Route::get('/agreements/{agreement}/pdf/download', [PdfController::class, 'exportAgreementPdf'])
        ->name('agreements.pdf.download');
    Route::get('/clients/{client}/pdf/preview', [PdfController::class, 'previewClientPdf'])
        ->name('clients.pdf.preview');

    Route::get('/clients/{client}/pdf/download', [PdfController::class, 'exportClientPdf'])
        ->name('clients.pdf.download');
});
        Route::post('/sales-reps/{salesRep}/update-photo', [SalesRepController::class, 'updatePhoto'])->name('sales-reps.updatePhoto');
Route::delete('/admin/client-edit-request/delete/{client_request}', [AdminClientEditRequestController::class, 'destroy'])->name('admin.client-request.delete');
Route::get('/admin/client-edit-requests/{client}/edit/{client_request}', [AdminClientEditRequestController::class, 'edit'])->name('admin.client-request.edit');
Route::put('/admin/client-edit-requests/{client}/update/{client_request}', [AdminClientEditRequestController::class, 'update'])->name('admin.client-request.update');
Route::get('/admin/client-edit-requests/{client}/review/{client_request}', [AdminClientEditRequestController::class, 'review'])->name('admin.client-request.review');
Route::get('/admin/client-pended-edit-requests', [AdminClientEditRequestController::class, 'pendedRequests'])->name('admin.client-edit-requests.pended');
Route::get('/admin/client-requests/{client}/edit/{client_request}', [ClientRequestController::class, 'edit'])->name('admin.chat-client-request.edit');
Route::delete('/admin/client-requests/{client}/delete/{client_request}', [ClientRequestController::class, 'destroy'])->name('admin.chat-client-request.destroy');
Route::put('/admin/client-requests/{client}/update/{client_request}', [ClientRequestController::class, 'update'])->name('admin.chat-client-request.update');
Route::get('/admin/client-requests/{client}/review/{client_request}', [ClientRequestController::class, 'review'])->name('admin.chat-client-request.review');
Route::get('/client-edit-requests/{clientEditRequest}', [ClientEditRequestController::class, 'review'])
    ->name('client-edit-requests.show')
    ->middleware('auth');
Route::get('/admin/client-edit-requests/{clientEditRequest}', [AdminClientEditRequestController::class, 'show'])
    ->name('admin.client-edit-requests.show')
    ->middleware(['auth']);
/*
|--------------------------------------------------------------------------
| Notifications
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'web', \App\Http\Middleware\MarkNotificationAsRead::class])->group(function () {
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{notification}/mark-as-read', [NotificationController::class, 'markAsRead']);
});

Route::post('/notifications/mark-all-as-read', [NotificationController::class, 'markAllAsRead'])
    ->middleware(['auth', 'web'])
    ->name('notifications.markAllAsRead');

Route::get('/notification/open', [NotificationController::class, 'open'])
    ->middleware(['auth', \App\Http\Middleware\MarkNotificationAsRead::class])
    ->name('notification.redirect');
// Sales Rep Commission Routes (for individual sales reps)
Route::middleware(['auth'])->group(function () {
    // Sales rep viewing their own commissions
    Route::get('/sales-reps/{salesRep}/commissions', [AdminCommissionController::class, 'index'])
        ->name('sales-reps.commissions.index');

    Route::get('/sales-reps/{salesRep}/commissions/{commission}', [SalesRepCommissionController::class, 'show'])
        ->name('sales-reps.commissions.show');

    Route::get('/sales-reps/{salesRep}/commissions/{commission}/export', [SalesRepCommissionController::class, 'export'])
        ->name('sales-reps.commissions.export');
});

// Admin Commission Routes (for viewing all commissions)
Route::middleware(['auth'])->group(function () {
    // Admin viewing all commissions
    Route::get('/commissions', [AdminCommissionController::class, 'index'])
        ->name('commissions.index');

    Route::get('/commissions/{commission}', [AdminCommissionController::class, 'show'])
        ->name('commissions.show');

    Route::get('/commissions/{commission}/export', [AdminCommissionController::class, 'export'])
        ->name('commissions.export');

    // Monthly commission reports
    Route::get('/commissions/reports/monthly', [AdminCommissionController::class, 'monthly'])
        ->name('commissions.reports.monthly');

    Route::get('/commissions/reports/sales-reps', [AdminCommissionController::class, 'bySalesRep'])
        ->name('commissions.reports.by-sales-rep');

    Route::get('/commissions/reports/services', [AdminCommissionController::class, 'byService'])
        ->name('commissions.reports.by-service');
});
Route::get('/preview-salesRep-pdf', function () {
    return view('preview.salesRep');
})->middleware('auth');

Route::get('/preview-agreement-pdf', function () {
    return view('preview.agreement');
})->middleware('auth');

Route::get('/table', function () {
    return view('components.table');
})->middleware('auth');
Route::get('/notifications/list', function () {
    return view('notifications.notifications_list', [
        'notifications' => Auth::user()->notifications()->latest()->take(20)->get()
    ]);
})->middleware('auth');
Route::middleware('auth')->group(function () {
    Route::get('/chat-clients', [ClientController::class, 'chatClients']);
    Route::get('/clientChat', Index::class)->name('chat.index');
    Route::get('client/{client}/Chat/{conversation}', Chat::class)->name('client.chat');
    Route::get('client/{client}/message', [ChatController::class, 'message'])->name('client.message');
    Route::get('/chat/unread-count', [ChatController::class, 'unreadConversationsCount']);


});

