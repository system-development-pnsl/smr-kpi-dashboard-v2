<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\KpiController;
use App\Http\Controllers\FinancialController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CompanySettingController;
use App\Http\Controllers\HomeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

Route::get('/track', function (Request $request) {
    $source = $request->utm_source;
    Log::info('User from: ' . $source);
    return redirect('https://t.me/pok_puthea');
});


// ── Auth ──────────────────────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/login',  [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/forgot-password',       [AuthController::class, 'showForgot'])->name('password.request');
    Route::post('/forgot-password',      [AuthController::class, 'sendReset'])->name('password.email');
    Route::get('/reset-password/{token}', [AuthController::class, 'showReset'])->name('password.reset');
    Route::post('/reset-password',       [AuthController::class, 'resetPassword'])->name('password.update');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// ── Authenticated ─────────────────────────────────────────────────────────────
Route::middleware('auth')->group(function () {

    // Home greeting (default landing after login)
    Route::get('/',          [HomeController::class,    'index'])->name('home');

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/search',    [DashboardController::class, 'search'])->name('search');

    // Tasks
    Route::resource('tasks', TaskController::class);
    Route::patch('tasks/{task}/status', [TaskController::class, 'updateStatus'])->name('tasks.update-status');
    Route::post('tasks/{task}/comments', [TaskController::class, 'addComment'])->name('tasks.comment');
    Route::post('tasks/{task}/subtasks', [TaskController::class, 'addSubtask'])->name('tasks.subtask');
    Route::post('tasks/bulk',           [TaskController::class, 'bulk'])->name('tasks.bulk');

    // KPI
    Route::prefix('kpi')->name('kpi.')->group(function () {
        Route::get('/',                         [KpiController::class, 'index'])->name('index');
        Route::get('/{kpi}',                    [KpiController::class, 'show'])->name('show');
        Route::post('/{kpi}/entry',             [KpiController::class, 'addEntry'])->name('entry');
        Route::get('/action-plan/{plan}',       [KpiController::class, 'actionPlan'])->name('action-plan');
        Route::post('/action-plan/{plan}/goal', [KpiController::class, 'addGoal'])->name('goal.store');
    });

    // Financial (role-restricted via middleware in controller)
    Route::prefix('financial')->name('financial.')->group(function () {
        Route::get('/',                         [FinancialController::class, 'index'])->name('index');
        Route::post('/cash-flow',               [FinancialController::class, 'storeCashFlow'])->name('cash-flow.store');
        Route::post('/bank-balance/{account}',  [FinancialController::class, 'updateBalance'])->name('balance.update');
        Route::get('/transactions',             [FinancialController::class, 'transactions'])->name('transactions');
    });

    // Documents
    Route::prefix('documents')->name('documents.')->group(function () {
        Route::get('/',                             [DocumentController::class, 'index'])->name('index');
        Route::post('/upload',                      [DocumentController::class, 'upload'])->name('upload');
        Route::get('/{document}',                   [DocumentController::class, 'show'])->name('show');
        Route::post('/{document}/confirm',          [DocumentController::class, 'confirm'])->name('confirm');
        Route::delete('/{document}',                [DocumentController::class, 'destroy'])->name('destroy');
        Route::get('/{document}/download',          [DocumentController::class, 'download'])->name('download');
    });

    // Reports
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/',                     [ReportController::class, 'index'])->name('index');
        Route::get('/{report}',             [ReportController::class, 'show'])->name('show');
        Route::get('/{report}/download',    [ReportController::class, 'download'])->name('download');
    });

    // Notifications
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/',         [NotificationController::class, 'index'])->name('index');
        Route::put('/{id}/read', [NotificationController::class, 'markRead'])->name('read');
        Route::put('/read-all', [NotificationController::class, 'markAllRead'])->name('read-all');
        Route::delete('/{id}',  [NotificationController::class, 'destroy'])->name('destroy');
    });

    // Users (GM only)
    Route::middleware('role:owner,general_manager,agm')->group(function () {
        Route::resource('users', UserController::class)->except(['show']);
    });

    // Profile
    Route::get('/profile',    [UserController::class, 'profile'])->name('profile');
    Route::put('/profile',    [UserController::class, 'updateProfile'])->name('profile.update');
    Route::put('/password',   [UserController::class, 'changePassword'])->name('password.change');
    Route::post('/profile/photo', [UserController::class, 'updatePhoto'])->name('profile.photo');

    // Settings
    Route::get('/settings/company',        [CompanySettingController::class, 'index'])->name('settings.company');
    Route::put('/settings/company',        [CompanySettingController::class, 'update'])->name('settings.company.update');
    Route::post('/settings/company/logo',  [CompanySettingController::class, 'uploadLogo'])->name('settings.company.logo');
});
