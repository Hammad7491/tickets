<?php

use Illuminate\Support\Facades\Route;

// ---------- Controllers ----------
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\SocialController;
use App\Http\Controllers\Admin\ClientController;
use App\Http\Controllers\Admin\ReviewController;
use App\Http\Controllers\Admin\TicketController;
use App\Http\Controllers\Admin\WinnerController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\User\UserTicketController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\User\UserDashboardController;
use App\Http\Controllers\Admin\PurchaseOrderController; // if you use it
use App\Http\Controllers\Admin\SiteController;          // if you use it
use App\Http\Controllers\Chatbot\ChatbotController;     // if you use it
use App\Http\Controllers\Chatbot\UserNameController;    // if you use it

// ==========================================================================
// Public
// ==========================================================================
Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login',     [AuthController::class, 'loginform'])->name('loginform');
Route::post('/login',    [AuthController::class, 'login'])->name('login');
Route::get('/register',  [AuthController::class, 'registerform'])->name('registerform');
Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/logout',   [AuthController::class, 'logout'])->name('logout');

Route::view('/error', 'auth.errors.error403')->name('auth.errors.error403');

// Social auth
Route::get('login/google',            [SocialController::class, 'redirectToGoogle'])->name('google.login');
Route::get('login/google/callback',   [SocialController::class, 'handleGoogleCallback']);
Route::get('login/facebook',          [SocialController::class, 'redirectToFacebook'])->name('facebook.login');
Route::get('login/facebook/callback', [SocialController::class, 'handleFacebookCallback']);

// ==========================================================================
// Admin area (auth required, all admin routes in one group)
// ==========================================================================
Route::middleware('auth')
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        // Dashboard
        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

     

        // Users
        Route::resource('users', UserController::class)->except(['show'])
            ->middleware('can:view users');
        Route::post('users/{user}/block',   [UserController::class, 'block'])->name('users.block');
        Route::post('users/{user}/unblock', [UserController::class, 'unblock'])->name('users.unblock');
        Route::get('users/list', [UserController::class, 'listPage'])->name('users.list');

        // Roles & Permissions
        Route::resource('roles', RoleController::class)->middleware('can:view roles');
        Route::resource('permissions', PermissionController::class)->middleware('can:view permissions');

        // Tickets
        Route::resource('tickets', TicketController::class)->except(['show']);
        Route::get('tickets/image/{path}', [TicketController::class, 'image'])
            ->where('path', '.*')
            ->name('tickets.image');
        Route::get('tickets/download/{path}', [TicketController::class, 'download'])
            ->where('path', '.*')
            ->name('tickets.download');

        // ==================================================================
        // Reviews (requires role:admin — change to permission:... if needed)
        // ==================================================================
        // Route::middleware('role:admin')->group(function () {
            Route::get('reviews/pending', [ReviewController::class, 'pending'])->name('reviews.pending');
            Route::get('reviews/accepted', [ReviewController::class, 'accepted'])->name('reviews.accepted');
            Route::get('reviews/{purchase}/proof', [ReviewController::class, 'proofShow'])->name('reviews.proof.show');
            Route::get('reviews/{purchase}/proof/download', [ReviewController::class, 'proofDownload'])->name('reviews.proof.download');
            Route::put('reviews/{purchase}/accept', [ReviewController::class, 'accept'])->name('reviews.accept');
            Route::put('reviews/{purchase}/reject', [ReviewController::class, 'reject'])->name('reviews.reject');
        });
    // });

// ==========================================================================
// User portal (role:user)
// ==========================================================================
Route::middleware(['auth', 'role:user'])
    ->prefix('user')
    ->name('user.')
    ->group(function () {
        Route::get('dashboard', [UserDashboardController::class, 'index'])->name('dashboard');
    });

// Optional /dashboard alias
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [UserDashboardController::class, 'index'])
        ->name('users.dashboard');
});

// ==========================================================================
// Logged-in user actions
// ==========================================================================
Route::middleware(['auth'])->group(function () {
    Route::get('/user/ticketstatus', [UserTicketController::class, 'index'])->name('users.ticketstatus.index');
    Route::get('/user/ticketstatus/{purchase}/proof', [UserTicketController::class, 'proofShow'])->name('users.ticketstatus.proof.show');
    Route::get('/user/ticketstatus/{purchase}/proof/download', [UserTicketController::class, 'proofDownload'])->name('users.ticketstatus.proof.download');
    Route::post('/tickets/{ticket}/buy', [UserTicketController::class, 'buy'])->name('users.tickets.buy');
});



Route::delete('ticketstatus/{purchase}', [UserTicketController::class, 'destroy'])
    ->name('users.ticketstatus.destroy');

Route::get('/users/buy/create/{ticket}', [UserTicketController::class, 'create'])
    ->name('users.buy.create')
    ->middleware('auth');

// Handle buy submit (uses existing buy() method)
Route::post('/users/buy/{ticket}', [UserTicketController::class, 'buy'])
    ->name('users.buy.store')
    ->middleware('auth');

// ADMIN routes (manage)
Route::middleware(['auth']) // add your admin middleware if you have one
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::resource('winners', WinnerController::class)->except(['show']);
    });

// USER / PUBLIC list (read-only)
Route::get('/winners', [WinnerController::class, 'index'])->name('winners.index');