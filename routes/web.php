<?php

use Illuminate\Support\Facades\Route;

// Models
use App\Models\Winner;

// Auth controllers
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\SocialController;

// Admin controllers
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\TicketController;
use App\Http\Controllers\Admin\ReviewController;
use App\Http\Controllers\Admin\WinnerController;

// User controllers
use App\Http\Controllers\User\UserDashboardController;
use App\Http\Controllers\User\UserTicketController;

/*
|--------------------------------------------------------------------------
| Public
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    $tickets = \App\Models\Ticket::latest()->take(8)->get();
    $winners = \App\Models\Winner::latest()->take(5)->get();
    return view('welcome', compact('tickets', 'winners'));
})->name('welcome');

Route::view('/error', 'auth.errors.error403')->name('auth.errors.error403');
Route::view('/terms', 'term&condition')->name('terms.show');

// ===== Auth (login/register/logout) =====
// LOGIN
Route::get('/login',  [AuthController::class, 'loginform'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');

Route::get('/register',  [RegisteredUserController::class, 'create'])->name('register');
Route::post('/register', [RegisteredUserController::class, 'store'])->name('register.store');  // submit signup

// Put near your auth routes
Route::get('/registerform', [\App\Http\Controllers\Auth\RegisteredUserController::class, 'create'])
    ->name('registerform'); // legacy alias → same page as /register

Route::get('/loginform', [\App\Http\Controllers\AuthController::class, 'loginform'])
    ->name('loginform'); // legacy alias → same page as /login

// LOGOUT
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ===== Social auth =====
Route::get('login/google',            [SocialController::class, 'redirectToGoogle'])->name('google.login');
Route::get('login/google/callback',   [SocialController::class, 'handleGoogleCallback']);
Route::get('login/facebook',          [SocialController::class, 'redirectToFacebook'])->name('facebook.login');
Route::get('login/facebook/callback', [SocialController::class, 'handleFacebookCallback']);

// Public winners list
Route::get('/winners', [WinnerController::class, 'index'])->name('winners.index');

/*
|--------------------------------------------------------------------------
| Admin (auth required)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        // Dashboard
        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Users
        Route::resource('users', UserController::class)->except(['show'])->middleware('can:view users');
        Route::post('users/{user}/block',   [UserController::class, 'block'])->name('users.block');
        Route::post('users/{user}/unblock', [UserController::class, 'unblock'])->name('users.unblock');
        Route::get('users/list',            [UserController::class, 'listPage'])->name('users.list');

        // Roles & Permissions
        Route::resource('roles',        RoleController::class)->middleware('can:view roles');
        Route::resource('permissions',  PermissionController::class)->middleware('can:view permissions');

        // Tickets
        Route::resource('tickets', TicketController::class)->except(['show']);
        Route::get('tickets/image/{path}',    [TicketController::class, 'image'])->where('path', '.*')->name('tickets.image');
        Route::get('tickets/download/{path}', [TicketController::class, 'download'])->where('path', '.*')->name('tickets.download');

        // Reviews (admin only)
        Route::prefix('reviews')->name('reviews.')->middleware('role:admin')->group(function () {
            Route::get('pending',   [ReviewController::class, 'pending'])->name('pending');
            Route::get('accepted',  [ReviewController::class, 'accepted'])->name('accepted');

            Route::get('{purchase}/proof',           [ReviewController::class, 'proofShow'])->name('proof.show');
            Route::get('{purchase}/proof/download',  [ReviewController::class, 'proofDownload'])->name('proof.download');

            Route::put('{purchase}/accept', [ReviewController::class, 'accept'])->name('accept');
            Route::put('{purchase}/reject', [ReviewController::class, 'reject'])->name('reject');

            Route::delete('{purchase}',  [ReviewController::class, 'destroy'])->name('destroy');
            Route::post('bulk-delete',   [ReviewController::class, 'bulkDelete'])->name('bulkDelete');
        });

        // Winners (admin manage)
        Route::resource('winners', WinnerController::class)->except(['show']);
    });

/*
|--------------------------------------------------------------------------
| User portal (role:user)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:user'])
    ->prefix('user')
    ->name('user.')
    ->group(function () {
        Route::get('dashboard', [UserDashboardController::class, 'index'])->name('dashboard');
    });

// Optional /dashboard alias for verified users
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [UserDashboardController::class, 'index'])->name('users.dashboard');
});

/*
|--------------------------------------------------------------------------
| Logged-in user actions
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    Route::get('/user/ticketstatus',                             [UserTicketController::class, 'index'])->name('users.ticketstatus.index');
    Route::get('/user/ticketstatus/{purchase}/proof',            [UserTicketController::class, 'proofShow'])->name('users.ticketstatus.proof.show');
    Route::get('/user/ticketstatus/{purchase}/proof/download',   [UserTicketController::class, 'proofDownload'])->name('users.ticketstatus.proof.download');

    Route::post('/tickets/{ticket}/buy', [UserTicketController::class, 'buy'])->name('users.tickets.buy');

    // delete a user's own ticket request
    Route::delete('ticketstatus/{purchase}', [UserTicketController::class, 'destroy'])->name('users.ticketstatus.destroy');

    // Buy flow (create + store)
    Route::get('/users/buy/create/{ticket}', [UserTicketController::class, 'create'])->name('users.buy.create');
    Route::post('/users/buy/{ticket}',       [UserTicketController::class, 'buy'])->name('users.buy.store');
});
