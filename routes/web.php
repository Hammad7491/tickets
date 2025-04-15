<?php
use App\Http\Controllers\Auth\SocialController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Chatbot\ChatbotController;
use App\Http\Controllers\Chatbot\UserNameController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', [AuthController::class, 'loginform'])->name('loginform');
Route::get('/register', [AuthController::class, 'registerform'])->name('registerform');
Route::post('register', [AuthController::class, 'register'])->name('register');;
Route::post('login', [AuthController::class, 'login'])->name('login');;

Route::post('logout', [AuthController::class, 'logout'])->name('logout');


Route::get('login/google', [SocialController::class, 'redirectToGoogle'])->name('google.login');
Route::get('login/google/callback', [SocialController::class, 'handleGoogleCallback']);

Route::get('login/facebook', [SocialController::class, 'redirectToFacebook'])->name('facebook.login');
Route::get('login/facebook/callback', [SocialController::class, 'handleFacebookCallback']);

// Route to display the chat interface.
Route::get('/chat', [UserNameController::class, 'index'])->name('chatname.index');
Route::post('/chat/send', [UserNameController::class, 'sendMessage'])->name('chatname.post');



Route::get('/chatbot', [ChatbotController::class, 'index'])->name('chatbot.index');
Route::post('/chatbot/send', [ChatbotController::class, 'sendMessage'])->name('chatbot.post');



Route::get('/error', [AuthController::class, 'error403'])->name('auth.errors.error403');


Route::middleware(['auth'])->group(function () {


    Route::prefix('admin')->middleware(['role:admin'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('admin.dashboard');
});



Route::prefix('user')->middleware(['role:user'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'userDashboard'])->name('user.dashboard');
});


});





