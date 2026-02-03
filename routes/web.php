<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    // Redirect to login if not authenticated, otherwise redirect to dashboard
    if (auth()->check()) {
        return auth()->user()->isAdmin()
            ? redirect()->route('admin.dashboard')
            : redirect()->route('employee.dashboard');
    }
    return redirect()->route('login');
});

// Admin Routes (protected by admin middleware)
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', \App\Http\Livewire\Admin\Dashboard::class)->name('dashboard');
    Route::get('/users', \App\Http\Livewire\Admin\UserManagement::class)->name('users');
    Route::get('/users/{id}', \App\Http\Livewire\Admin\UserDetail::class)->name('user-detail');
    Route::get('/categories', \App\Http\Livewire\Admin\Categories::class)->name('categories');
    Route::get('/wallets', \App\Http\Livewire\Admin\WalletManagement::class)->name('wallets');
    Route::get('/wallet-history', \App\Http\Livewire\Admin\WalletHistory::class)->name('wallet-history');
    Route::get('/reports', \App\Http\Livewire\Admin\UserExpenseReport::class)->name('reports');
});

// Employee Routes (protected by auth middleware)
Route::middleware(['auth'])->prefix('employee')->name('employee.')->group(function () {
    Route::get('/dashboard', \App\Http\Livewire\Employee\Dashboard::class)->name('dashboard');
    Route::get('/expenses', \App\Http\Livewire\Employee\Expenses::class)->name('expenses');
    Route::get('/add-expense', \App\Http\Livewire\Employee\AddExpense::class)->name('add-expense');
    Route::get('/wallet-history', \App\Http\Livewire\Employee\TopupHistory::class)->name('wallet-history');
});

// Generic dashboard route that redirects based on role
Route::middleware(['auth'])->get('/dashboard', function () {
    return auth()->user()->isAdmin()
        ? redirect()->route('admin.dashboard')
        : redirect()->route('employee.dashboard');
})->name('dashboard');

require __DIR__.'/auth.php';
