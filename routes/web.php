<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HospitalDashboardController;
use App\Http\Controllers\Saas\SaasDashboardController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

Route::get('/', [App\Http\Controllers\Saas\CmsController::class, 'landing'])->name('landing');

Route::middleware('auth')->group(function () {
    Route::get('/hospital/dashboard', [HospitalDashboardController::class, 'index'])->name('hospital.dashboard');
    Route::get('/hospital/settings', [HospitalSettingsController::class, 'index'])->name('hospital.settings.index');
    Route::patch('/hospital/settings', [HospitalSettingsController::class, 'update'])->name('hospital.settings.update');

    // Super Admin only routes
    Route::middleware('super_admin')->prefix('saas')->name('saas.')->group(function () {
        // Dashboard
        Route::get('/dashboard', [SaasDashboardController::class, 'index'])->name('dashboard');

        // Hospitals management
        Route::get('/hospitals', [Saas\HospitalController::class, 'index'])->name('hospitals.index');
        Route::get('/hospitals/{hospital}/edit', [Saas\HospitalController::class, 'edit'])->name('hospitals.edit');
        Route::patch('/hospitals/{hospital}', [Saas\HospitalController::class, 'update'])->name('hospitals.update');
        Route::get('/hospitals/{hospital}/impersonate', [Saas\HospitalController::class, 'impersonate'])->name('hospitals.impersonate');
        Route::get('/stop-impersonation', [Saas\HospitalController::class, 'stopImpersonation'])->name('stop-impersonation');

        // Hospital Types
        Route::get('/hospital-types', [Saas\HospitalTypeController::class, 'index'])->name('hospital-types.index');
        Route::post('/hospital-types', [Saas\HospitalTypeController::class, 'store'])->name('hospital-types.store');
        Route::get('/hospital-types/{hospitalType}/edit', [Saas\HospitalTypeController::class, 'edit'])->name('hospital-types.edit');
        Route::patch('/hospital-types/{hospitalType}', [Saas\HospitalTypeController::class, 'update'])->name('hospital-types.update');
        Route::delete('/hospital-types/{hospitalType}', [Saas\HospitalTypeController::class, 'destroy'])->name('hospital-types.destroy');

        // Subscription Plans
        Route::get('/plans', [Saas\SubscriptionPlanController::class, 'index'])->name('plans.index');
        Route::get('/plans/create', [Saas\SubscriptionPlanController::class, 'create'])->name('plans.create');
        Route::post('/plans', [Saas\SubscriptionPlanController::class, 'store'])->name('plans.store');
        Route::get('/plans/{plan}/edit', [Saas\SubscriptionPlanController::class, 'edit'])->name('plans.edit');
        Route::patch('/plans/{plan}', [Saas\SubscriptionPlanController::class, 'update'])->name('plans.update');
        Route::delete('/plans/{plan}', [Saas\SubscriptionPlanController::class, 'destroy'])->name('plans.destroy');

        // Transactions
        Route::get('/transactions', [Saas\TransactionController::class, 'index'])->name('transactions.index');

        // Subscribers
        Route::get('/subscribers', [Saas\SubscriberController::class, 'index'])->name('subscribers.index');
        Route::delete('/subscribers/{subscriber}', [Saas\SubscriberController::class, 'destroy'])->name('subscribers.destroy');

        // Enquiries
        Route::get('/enquiries', [Saas\EnquiryController::class, 'index'])->name('enquiries.index');
        Route::get('/enquiries/{enquiry}', [Saas\EnquiryController::class, 'show'])->name('enquiries.show');
        Route::patch('/enquiries/{enquiry}/mark-read', [Saas\EnquiryController::class, 'markRead'])->name('enquiries.mark-read');
        Route::delete('/enquiries/{enquiry}', [Saas\EnquiryController::class, 'destroy'])->name('enquiries.destroy');

        // CMS
        Route::get('/cms', [Saas\CmsController::class, 'index'])->name('cms.index');
        Route::get('/cms/{section}/edit', [Saas\CmsController::class, 'edit'])->name('cms.edit');
        Route::patch('/cms/{section}', [Saas\CmsController::class, 'update'])->name('cms.update');

        // Settings
        Route::get('/settings', [Saas\SettingsController::class, 'index'])->name('settings.index');
        Route::patch('/settings', [Saas\SettingsController::class, 'update'])->name('settings.update');
    });
});
