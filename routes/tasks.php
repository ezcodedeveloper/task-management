<?php

use App\Http\Controllers\Task\TaskController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::middleware('auth')->group(function () {
    Route::get('tasks/', [TaskController::class, 'list'])->name('tasks.list');
    Route::post('tasks/store-task', [TaskController::class, 'store'])->name('tasks.store');
    Route::get('tasks/get-tasks', [TaskController::class, 'taskDataTable'])->name('tasks.get-tasks');
    Route::delete('tasks/{id}', [TaskController::class, 'delete'])->name('tasks.delete');
    Route::put('tasks/update', [TaskController::class, 'update'])->name('patasksssword.update');

    // Route::get('settings/password', [PasswordController::class, 'edit'])->name('password.edit');
    // Route::put('settings/password', [PasswordController::class, 'update'])->name('password.update');

    // Route::get('settings/appearance', function () {
    //     return Inertia::render('settings/Appearance');
    // })->name('appearance');
});
