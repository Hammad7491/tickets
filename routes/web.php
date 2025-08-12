<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\SiteController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\SocialController;
use App\Http\Controllers\Admin\ClientController;
use App\Http\Controllers\Admin\TicketController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Chatbot\ChatbotController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Chatbot\UserNameController;
use App\Http\Controllers\User\UserDashboardController;
use App\Http\Controllers\Admin\PurchaseOrderController;

// Public
Route::view('/', 'welcome');

Route::get('/login',      [AuthController::class, 'loginform'])->name('loginform');
Route::post('/login',     [AuthController::class, 'login'])->name('login');
Route::get('/register',   [AuthController::class, 'registerform'])->name('registerform');
Route::post('/register',  [AuthController::class, 'register'])->name('register');
Route::post('/logout',    [AuthController::class, 'logout'])->name('logout');
Route::view('/error',     'auth.errors.error403')->name('auth.error403');

// Social
Route::get('login/google',   [SocialController::class, 'redirectToGoogle'])->name('google.login');
Route::get('login/google/callback', [SocialController::class, 'handleGoogleCallback']);
Route::get('login/facebook', [SocialController::class, 'redirectToFacebook'])->name('facebook.login');
Route::get('login/facebook/callback', [SocialController::class, 'handleFacebookCallback']);

// Authenticated
Route::middleware('auth')->group(function () {



     // Admin area
     Route::prefix('admin')
          ->name('admin.')
          ->group(function () {




               Route::resource('clients', ClientController::class)
                    ->middleware('can:view clients');

               // Dashboard (single index action)
               Route::resource('dashboard', DashboardController::class)
                    ->only('index')
                    ->names(['index' => 'dashboard'])
                    ->middleware('can:view dashboard');

        
               Route::resource('users', UserController::class)

                    ->middleware('can:view users');

               // Roles CRUD
               Route::resource('roles', RoleController::class)

                    ->middleware('can:view roles');

               // Permissions CRUD
               Route::resource('permissions', PermissionController::class)

                    ->middleware('can:view permissions');
          });
});

Route::prefix('admin')->name('admin.')->group(function () {
    Route::resource('users', \App\Http\Controllers\Admin\UserController::class);

    Route::post('users/{user}/block',  [\App\Http\Controllers\Admin\UserController::class, 'block'])->name('users.block');
    Route::post('users/{user}/unblock',[\App\Http\Controllers\Admin\UserController::class, 'unblock'])->name('users.unblock');
});


// routes/web.php
Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function () {
    Route::resource('tickets', \App\Http\Controllers\Admin\TicketController::class);
});

Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function () {
    // List page (no user param)
    Route::get('users/list', [UserController::class, 'listPage'])->name('users.list');

    // Resource routes WITHOUT 'show'
    Route::resource('users', UserController::class)->except(['show']);

    // Block / Unblock
    Route::post('users/{user}/block',   [UserController::class, 'block'])->name('users.block');
    Route::post('users/{user}/unblock', [UserController::class, 'unblock'])->name('users.unblock');
});

Route::get('/user/dashboard', [UserDashboardController::class, 'index'])
    ->middleware('auth')
    ->name('users.dashboard');   // or ->name('user.dashboard') if you prefer