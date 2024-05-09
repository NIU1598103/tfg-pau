<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PreferencesController;
use App\Http\Controllers\DayController;
use App\Http\Controllers\MonthController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\WeekController;

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
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');


    //crear aqui
    Route::get('/posts', [PostController::class, 'index'])->name('posts.index');
    Route::get('/posts/create', [PostController::class, 'create'])->name('posts.create');
    Route::post('/posts', [PostController::class, 'store'])->name('posts.store');
    Route::get('/posts/{post}/edit', [PostController::class, 'show'])->name('posts.show');
    Route::post('/posts/update', [PostController::class, 'update'])->name('posts.update');
    Route::get('/posts/{post}/delete', [PostController::class, 'destroy'])->name('posts.destroy');

    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::post('/users/update', [UserController::class, 'update'])->name('users.update');
    Route::post('/users/update_blocked_days', [UserController::class, 'update_blocked_days'])->name('users.update_blocked_days');
    Route::get('/users/{id}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::get('/users/{id}/delete', [UserController::class, 'delete'])->name('users.delete');
    Route::post('/users/delete_confirm', [UserController::class, 'delete_confirm'])->name('users.delete_confirm');
    
    Route::get('/preferences', [PreferencesController::class, 'index'])->name('preferences.index');
    Route::get('/feedback', [FeedbackController::class, 'index'])->name('feedback.index');
    
    Route::resource('days', DayController::class);
    Route::resource('months', MonthController::class);
    Route::get('/months/{id}/edit', [MonthController::class, 'edit'])->name('months.edit');
    Route::get('/months/{id}/consult', [MonthController::class, 'consult'])->name('months.consult');
    Route::get('/months/{id}/see_guards', [MonthController::class, 'see_guards'])->name('months.see_guards');
    Route::get('/months', [MonthController::class, 'index'])->name('months.index');
    // Route::post('/users/updateBlockDays', [DayController::class, 'update'])->name('days.update');
    Route::post('/months/organize_guards', [MonthController::class, 'organize_guards'])->name('months.organize_guards');
    Route::post('/months/clean_guards', [MonthController::class, 'clean_guards'])->name('months.clean_guards');
    
    Route::get('/weeks', [WeekController::class, 'index'])->name('weeks.index');
    Route::get('/weeks/{week}/edit', [WeekController::class, 'edit'])->name('weeks.edit');
    Route::post('/weeks/update_guard_transplant', [WeekController::class, 'update_guard_transplant'])->name('weeks.update_guard_transplant');
});
