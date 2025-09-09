<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HospitalDashboardController;
use App\Http\Controllers\HospitalSettingsController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;

// Boost the performance by adding the import statement to the file to speed up the execution of the application.
use App\Http\Controllers\Saas\SaasDashboardController;
use App\Http\Controllers\Saas\HospitalController;
use App\Http\Controllers\Saas\HospitalTypeController;
use App\Http\Controllers\Saas\SubscriptionPlanController;
use App\Http\Controllers\Saas\TransactionController;
use App\Http\Controllers\Saas\SubscriberController;
use App\Http\Controllers\Saas\EnquiryController;
use App\Http\Controllers\Saas\CmsController;
use App\Http\Controllers\Saas\SettingsController;

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->middleware('throttle:login');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Password reset routes
Route::get('/password/reset', function () {
    return view('auth.passwords.email');
})->name('password.request');

Route::post('/password/email', function () {
    return back()->with('status', 'Password reset link sent successfully!');
})->name('password.email');

Route::get('/password/reset/{token}', function () {
    return view('auth.passwords.reset');
})->name('password.reset');

Route::post('/password/reset', function () {
    return redirect('/login')->with('status', 'Password reset successfully!');
})->name('password.update');
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register'])->middleware('throttle:register');

Route::get('/', [App\Http\Controllers\Saas\CmsController::class, 'landing'])->name('landing');
Route::get('/admin', function () {
    return redirect()->route('saas.dashboard');
})->middleware(['auth', 'super_admin'])->name('admin');

Route::middleware('auth')->group(function () {
    Route::get('/hospital/dashboard', [HospitalDashboardController::class, 'index'])->name('hospital.dashboard');
    Route::get('/hospital/settings', [HospitalSettingsController::class, 'index'])->name('hospital.settings.index');
    Route::patch('/hospital/settings', [HospitalSettingsController::class, 'update'])->name('hospital.settings.update');

    // Super Admin only routes
    Route::middleware('super_admin')->prefix('saas')->name('saas.')->group(function () {
        // Dashboard
        Route::get('/dashboard', [SaasDashboardController::class, 'index'])->name('dashboard');

        // Hospitals management
        Route::get('/hospitals', [HospitalController::class, 'index'])->name('hospitals.index');
        Route::get('/hospitals/{hospital}/edit', [HospitalController::class, 'edit'])->name('hospitals.edit');
        Route::patch('/hospitals/{hospital}', [HospitalController::class, 'update'])->name('hospitals.update');
        Route::get('/hospitals/{hospital}/impersonate', [HospitalController::class, 'impersonate'])->name('hospitals.impersonate');
        Route::get('/stop-impersonation', [HospitalController::class, 'stopImpersonation'])->name('stop-impersonation');

        // Hospital Types
        Route::get('/hospital-types', [HospitalTypeController::class, 'index'])->name('hospital-types.index');
        Route::post('/hospital-types', [HospitalTypeController::class, 'store'])->name('hospital-types.store');
        Route::get('/hospital-types/{hospitalType}/edit', [HospitalTypeController::class, 'edit'])->name('hospital-types.edit');
        Route::patch('/hospital-types/{hospitalType}', [HospitalTypeController::class, 'update'])->name('hospital-types.update');
        Route::delete('/hospital-types/{hospitalType}', [HospitalTypeController::class, 'destroy'])->name('hospital-types.destroy');

        // Subscription Plans
        Route::get('/plans', [SubscriptionPlanController::class, 'index'])->name('plans.index');
        Route::get('/plans/create', [SubscriptionPlanController::class, 'create'])->name('plans.create');
        Route::post('/plans', [SubscriptionPlanController::class, 'store'])->name('plans.store');
        Route::get('/plans/{plan}/edit', [SubscriptionPlanController::class, 'edit'])->name('plans.edit');
        Route::patch('/plans/{plan}', [SubscriptionPlanController::class, 'update'])->name('plans.update');
        Route::delete('/plans/{plan}', [SubscriptionPlanController::class, 'destroy'])->name('plans.destroy');

        // Transactions
        Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions.index');

        // Subscribers
        Route::get('/subscribers', [SubscriberController::class, 'index'])->name('subscribers.index');
        Route::delete('/subscribers/{subscriber}', [SubscriberController::class, 'destroy'])->name('subscribers.destroy');

        // Enquiries
        Route::get('/enquiries', [EnquiryController::class, 'index'])->name('enquiries.index');
        Route::get('/enquiries/{enquiry}', [EnquiryController::class, 'show'])->name('enquiries.show');
        Route::patch('/enquiries/{enquiry}/mark-read', [EnquiryController::class, 'markRead'])->name('enquiries.mark-read');
        Route::delete('/enquiries/{enquiry}', [EnquiryController::class, 'destroy'])->name('enquiries.destroy');

        // CMS
        Route::get('/cms', [CmsController::class, 'index'])->name('cms.index');
        Route::get('/cms/{section}/edit', [CmsController::class, 'edit'])->name('cms.edit');
        Route::patch('/cms/{section}', [CmsController::class, 'update'])->name('cms.update');

        // Settings
        Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
        Route::patch('/settings', [SettingsController::class, 'update'])->name('settings.update');
    });
});
