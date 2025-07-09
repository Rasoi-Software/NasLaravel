<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\CityController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\SubscriptionController;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\StripeController;
use App\Http\Controllers\HostingController;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return view('welcome');
});

Route::post('/logout', function () {
    Auth::logout();
    return redirect('/login');
})->name('logout');

Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::get('admin/login', [LoginController::class, 'showLoginForm'])->name('admin.login');
Route::post('admin/login', [LoginController::class, 'login'])->name('admin.login.submit');
Route::post('admin/logout', [LoginController::class, 'logout'])->name('admin.logout');

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/admin/subscriptions', [SubscriptionController::class, 'index'])->name('subscription.index');
    Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
    Route::resource('users', UserController::class);
});


Route::middleware(['auth', 'role:user'])->group(function () {
    Route::get('/user/dashboard', function () {
        return view('user.dashboard');
    });
});


Route::post('/webhook/stripe', [StripeWebhookController::class, 'handle']);

Route::get('/', [HostingController::class, 'index'])->name('hosting.plans');
Route::post('/hosting/subscribe', [HostingController::class, 'subscribe'])->name('hosting.subscribe');
Route::get('/checkout/success', [HostingController::class, 'success'])->name('checkout.success');
Route::get('/checkout/cancel', [HostingController::class, 'cancel'])->name('checkout.cancel');
