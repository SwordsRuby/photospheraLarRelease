<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AlbumController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\MainController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DownloadController;


// Main page
Route::get('/', [MainController::class, 'index'])->name('home');

// Guest routes (auth)
Route::middleware('guest')->group(function () {
    Route::post('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/register', [AuthController::class, 'register'])->name('register');
});

// Authenticated routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

// Email verification routes
Route::prefix('verification')->name('verification.')->group(function () {
    Route::get('/{userId}', [AuthController::class, 'showVerificationForm'])->name('show');
    Route::post('/{userId}/verify', [AuthController::class, 'verifyCode'])->name('verify');
    Route::post('/{userId}/resend', [AuthController::class, 'resendCode'])->name('resend');
});

// Images routes
Route::prefix('images')->name('images.')->group(function () {
    // Public
    Route::get('/', [ImageController::class, 'index'])->name('index');
    Route::get('/{id}/show', [ImageController::class, 'show'])->name('show');

    // Auth required
    Route::middleware('auth')->group(function () {
        Route::get('/create', [ImageController::class, 'create'])->name('create');
        Route::post('/create', [ImageController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [ImageController::class, 'edit'])->name('edit');
        Route::put('/{id}', [ImageController::class, 'update'])->name('update');
        Route::delete('/{id}', [ImageController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/like', [ImageController::class, 'like'])->name('like');
        Route::post('/{id}/favorite', [ImageController::class, 'favorite'])->name('favorite');

        // User's images management
        Route::get('/user/private', [ImageController::class, 'privateImages'])->name('user.private');
    });
});

// User routes (authenticated)
Route::middleware('auth')->prefix('user')->name('user.')->group(function () {
    // Profile
    Route::get('/favorites', [UserController::class, 'favorites'])->name('favorites');
    Route::get('/added', [UserController::class, 'added'])->name('added');
    Route::get('/private', [UserController::class, 'private'])->name('private');
    Route::get('/storage', [UserController::class, 'storage'])->name('storage');
    Route::post('/profile', [UserController::class, 'updateProfile'])->name('profile.update');

    // Albums
    Route::prefix('albums')->name('albums.')->group(function () {
        Route::get('/', [AlbumController::class, 'index'])->name('index');
        Route::get('/create', [AlbumController::class, 'create'])->name('create');
        Route::post('/create', [AlbumController::class, 'store'])->name('store');
        Route::get('/{id}', [AlbumController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [AlbumController::class, 'edit'])->name('edit');
        Route::put('/{id}', [AlbumController::class, 'update'])->name('update');
        Route::delete('/{id}', [AlbumController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/add-image', [AlbumController::class, 'addImage'])->name('addImage');
        Route::delete('/{albumId}/remove-image/{imageId}', [AlbumController::class, 'removeImage'])->name('removeImage');
    });

    // Additional user actions
    Route::delete('/account', [UserController::class, 'deleteAccount'])->name('account.delete');
});

// Subscription routes (authenticated)
Route::middleware('auth')->prefix('subscription')->name('subscription.')->group(function () {
    Route::get('/plans', [SubscriptionController::class, 'plans'])->name('plans');
    Route::get('/', [SubscriptionController::class, 'index'])->name('index');
    Route::get('/checkout/{plan}', [SubscriptionController::class, 'checkout'])->name('checkout');
    Route::post('/process', [SubscriptionController::class, 'processPayment'])->name('process');
    Route::get('/success', [SubscriptionController::class, 'paymentSuccess'])->name('success');
    Route::get('/check-payment/{paymentId}', [SubscriptionController::class, 'checkPaymentStatus'])->name('check.payment');
    Route::post('/cancel', [SubscriptionController::class, 'cancel'])->name('cancel');
});

// Admin routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Images
    Route::get('/images', [AdminController::class, 'images'])->name('images');
    Route::post('/images/{id}/approve', [AdminController::class, 'approveImage'])->name('images.approve');
    Route::delete('/images/{id}', [AdminController::class, 'destroyImage'])->name('images.destroy');

    // Users
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::post('/users/{id}/ban', [AdminController::class, 'banUser'])->name('users.ban');
    Route::post('/users/{id}/unban', [AdminController::class, 'unbanUser'])->name('users.unban');

    // Moderators
    Route::get('/moderators', [AdminController::class, 'moderators'])->name('moderators');
    Route::post('/moderators', [AdminController::class, 'createModerator'])->name('moderators.store');
    Route::post('/moderators/{id}/verify', [AdminController::class, 'verifyModerator'])->name('moderators.verify');
    Route::post('/moderators/{id}/resend-code', [AdminController::class, 'resendModeratorCode'])->name('moderators.resend');
    Route::delete('/moderators/{id}', [AdminController::class, 'destroyModerator'])->name('moderators.destroy');

    // Categories
    Route::get('/categories', [AdminController::class, 'categories'])->name('categories');
    Route::post('/categories', [AdminController::class, 'createCategory'])->name('categories.store');
    Route::put('/categories/{id}', [AdminController::class, 'updateCategory'])->name('categories.update');
    Route::delete('/categories/{id}', [AdminController::class, 'destroyCategory'])->name('categories.destroy');

    // Tags
    Route::get('/tags', [AdminController::class, 'tags'])->name('tags');
    Route::post('/tags', [AdminController::class, 'createTag'])->name('tags.store');
    Route::put('/tags/{id}', [AdminController::class, 'updateTag'])->name('tags.update');
    Route::delete('/tags/{id}', [AdminController::class, 'destroyTag'])->name('tags.destroy');
});

// Album routes (authenticated)
Route::middleware('auth')->prefix('user/albums')->name('user.albums.')->group(function () {
    // Share routes
    Route::post('/{id}/share/generate', [AlbumController::class, 'generateShareLink'])->name('share.generate');
    Route::post('/{id}/share/disable', [AlbumController::class, 'disableShareLink'])->name('share.disable');
    Route::post('/{id}/share/regenerate', [AlbumController::class, 'regenerateShareLink'])->name('share.regenerate');
});

// Public shared album routes (no auth required)
Route::prefix('share')->name('albums.')->group(function () {
    Route::get('/album/{token}', [AlbumController::class, 'showShared'])->name('shared');
    Route::get('/album/{token}/image/{imageId}', [AlbumController::class, 'showSharedImage'])->name('shared.image');
});


// Route for download image with watermark
Route::get('/images/download/{id}', [DownloadController::class, 'download'])->name('images.download');