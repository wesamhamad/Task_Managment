<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TasksController;
use App\Http\Controllers\ProjectController;

// Redirect users to login page as default
Route::get('/', function () {
    return view('auth.login');
})->name('login');

// Authentication Routes
Auth::routes();

// Task Routes for authenticated users
Route::get('/user-home', [TasksController::class, 'index'])->middleware('auth')->name('tasks.index');
Route::get('/user-home/create', [TasksController::class, 'create'])->middleware('auth')->name('tasks.create');
Route::post('/user-home', [TasksController::class, 'store'])->middleware('auth')->name('tasks.store');
Route::get('/user-home/{task}', [TasksController::class, 'show'])->middleware('auth')->name('tasks.show');
Route::get('/user-home/{task}/edit', [TasksController::class, 'edit'])->middleware('auth')->name('tasks.edit');
Route::put('/user-home/{task}', [TasksController::class, 'update'])->middleware('auth')->name('tasks.update');
Route::delete('/user-home/{task}', [TasksController::class, 'destroy'])->middleware('auth')->name('tasks.destroy');
Route::resource('tasks', TasksController::class)->middleware('auth');


// Project Routes for admin users
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin-home', [ProjectController::class, 'index'])->name('projects.index');
    Route::get('/admin-home/create', [ProjectController::class, 'create'])->name('projects.create');
    Route::post('/admin-home/store', [ProjectController::class, 'store'])->name('projects.store');
    Route::delete('/admin-home/{project}', [ProjectController::class, 'destroy'])->name('projects.destroy');
});