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
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


Route::get('/test', [DiffController::class, 'test']);
Route::get('/test2', [DiffController::class, 'test2'])->name('test2');
Route::get('/create', function () {
    return view('diff.create');
});
Route::post('/test3', [DiffController::class, 'store'])->name('test3');

Route::get('/project', [DiffController::class, 'index'])->name('project.index');
Route::post('/project', [DiffController::class, 'storeProject'])->name('project.store');

Route::get('/project/{projectName}', [DiffController::class, 'showProject'])->name('project.show');

require __DIR__.'/auth.php';
