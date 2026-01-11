<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\BorrowingController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Auth Routes (Public)
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected Auth Routes (Require Login)
Route::middleware('auth')->group(function () {
    Route::get('/password', [AuthController::class, 'showResetPassword'])->name('password.show');
    Route::post('/password', [AuthController::class, 'updatePassword'])->name('password.update');
});
Route::middleware('auth')->group(function () {
    
    // Redirect root to books
    Route::get('/', function () {
        return redirect()->route('books.index');
    });

    // Book Management Routes - Only Admins can CREATE/UPDATE/DELETE
    Route::middleware('admin')->group(function () {
        Route::get('/books/create', [BookController::class, 'create'])->name('books.create');
        Route::post('/books', [BookController::class, 'store'])->name('books.store');
        Route::get('/books/{book}/edit', [BookController::class, 'edit'])->name('books.edit');
        Route::put('/books/{book}', [BookController::class, 'update'])->name('books.update');
        Route::delete('/books/{book}', [BookController::class, 'destroy'])->name('books.destroy');
    });

    // All users can view books
    Route::get('/books', [BookController::class, 'index'])->name('books.index');
    Route::get('/books/{book}', [BookController::class, 'show'])->name('books.show');

    // User Management Routes - Only Admins
    Route::middleware('admin')->group(function () {
        Route::resource('users', UserController::class)->only(['index', 'show']);
        Route::put('users/{user}/toggle-permission', [UserController::class, 'togglePermission'])->name('users.toggle-permission');
    });

    // Borrowing Management Routes
    // Member can see their own borrowing records
    Route::get('borrowings', [BorrowingController::class, 'index'])->name('borrowings.index');
    
    // Admin only routes for managing borrowings
    Route::middleware('admin')->group(function () {
        Route::get('borrowings/create', [BorrowingController::class, 'create'])->name('borrowings.create');
        Route::post('borrowings', [BorrowingController::class, 'store'])->name('borrowings.store');
        Route::put('borrowings/{borrowing}/return', [BorrowingController::class, 'return'])->name('borrowings.return');
    });
    
    // Members can see their own borrowing history
    Route::get('users/{user}/borrowings', [BorrowingController::class, 'userBorrowings'])->name('borrowings.user');
});

