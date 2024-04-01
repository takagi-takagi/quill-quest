<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DiffController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/project', [DiffController::class, 'index'])->name('project.index');
    Route::post('/project', [DiffController::class, 'storeProject'])->name('project.store');

    Route::get('/project/{id}', [DiffController::class, 'showProject'])->name('project.show');

    Route::post('/project/{id}', [DiffController::class, 'storePlainText'])->name('text.storePlainText');

    Route::post('/project/{id}/setQuery', [DiffController::class, 'setQuery']);

    Route::post('/project/{id}/storeChatText', [DiffController::class, 'storeChatText'])->name('text.storeChatText');

    Route::delete('/project/{project}', [DiffController::class, 'destroyProject'])->name('project.destroy');

    Route::delete('/text/{text}', [DiffController::class, 'destroyText'])->name('text.destroy');
});

require __DIR__.'/auth.php';
