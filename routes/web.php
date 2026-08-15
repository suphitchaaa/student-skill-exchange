<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserSkillController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store']);
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);
});

Route::middleware('auth')->group(function () {
    Route::get('/account/suspended', fn () => view('account.suspended'))->name('account.suspended');
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});

Route::middleware(['auth', 'role:student', 'account.active'])->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('student.dashboard');
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/image', [ProfileController::class, 'storeImage'])->name('profile.image.store');
    Route::delete('/profile/image', [ProfileController::class, 'destroyImage'])->name('profile.image.destroy');
    Route::get('/my-skills', [UserSkillController::class, 'index'])->name('user-skills.index');
    Route::post('/my-skills', [UserSkillController::class, 'store'])->name('user-skills.store');
    Route::put('/my-skills/{userSkill}', [UserSkillController::class, 'update'])->name('user-skills.update');
    Route::delete('/my-skills/{userSkill}', [UserSkillController::class, 'destroy'])->name('user-skills.destroy');
});

Route::prefix('admin')->middleware(['auth', 'role:admin', 'account.active'])->group(function () {
    Route::get('/dashboard', AdminDashboardController::class)->name('admin.dashboard');
});
