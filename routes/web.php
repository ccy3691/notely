<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\NotesController;
use App\Http\Controllers\FlashcardController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/notes', [NotesController::class, 'index'])->middleware(['auth', 'verified'])->name('notes.index');
Route::post('/notes', [NotesController::class, 'store'])->middleware(['auth', 'verified'])->name('notes.store');
Route::get('/notes/create', [NotesController::class, 'create'])->middleware(['auth', 'verified'])->name('notes.create');
Route::get('/notes/{note}/edit', [NotesController::class, 'edit'])->middleware(['auth', 'verified'])->name('notes.edit');
Route::put('/notes/{note}', [NotesController::class, 'update'])->middleware(['auth', 'verified'])->name('notes.update');

Route::post('/flashcards/jobs', [FlashcardController::class, 'createFlashcardJob'])->middleware(['auth', 'verified'])->name('flashcards.jobs.create');
Route::get('/flashcards/jobs/{jobId}/progress', [FlashcardController::class, 'getFlashcardJobProgress'])->middleware(['auth', 'verified'])->name('flashcards.jobs.progress');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/flashcards', [FlashcardController::class, 'index'])->name('flashcards.index');
    Route::get('/flashcards/{id}', [FlashcardController::class, 'show'])->name('flashcards.show');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
