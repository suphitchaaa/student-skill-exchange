<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\ExchangeRequestController as AdminExchangeRequestController;
use App\Http\Controllers\Admin\SkillController as AdminSkillController;
use App\Http\Controllers\Admin\StudentController as AdminStudentController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExchangeRequestController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StudentSearchController;
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
    Route::get('/students', [StudentSearchController::class, 'index'])->name('students.index');
    Route::get('/students/{user}', [StudentSearchController::class, 'show'])->name('students.show');
    Route::get('/students/{user}/exchange-request', [ExchangeRequestController::class, 'create'])->name('exchange-requests.create');
    Route::post('/students/{user}/exchange-request', [ExchangeRequestController::class, 'store'])->name('exchange-requests.store');
    Route::get('/exchange-requests', [ExchangeRequestController::class, 'index'])->name('exchange-requests.index');
    Route::get('/exchange-requests/{exchangeRequest}', [ExchangeRequestController::class, 'show'])->name('exchange-requests.show');
    Route::patch('/exchange-requests/{exchangeRequest}/accept', [ExchangeRequestController::class, 'accept'])->name('exchange-requests.accept');
    Route::patch('/exchange-requests/{exchangeRequest}/reject', [ExchangeRequestController::class, 'reject'])->name('exchange-requests.reject');
    Route::patch('/exchange-requests/{exchangeRequest}/cancel', [ExchangeRequestController::class, 'cancel'])->name('exchange-requests.cancel');
    Route::patch('/exchange-requests/{exchangeRequest}/complete', [ExchangeRequestController::class, 'complete'])->name('exchange-requests.complete');
    Route::get('/my-skills', [UserSkillController::class, 'index'])->name('user-skills.index');
    Route::post('/my-skills', [UserSkillController::class, 'store'])->name('user-skills.store');
    Route::put('/my-skills/{userSkill}', [UserSkillController::class, 'update'])->name('user-skills.update');
    Route::delete('/my-skills/{userSkill}', [UserSkillController::class, 'destroy'])->name('user-skills.destroy');
});

Route::prefix('admin')->middleware(['auth', 'role:admin', 'account.active'])->group(function () {
    Route::get('/dashboard', AdminDashboardController::class)->name('admin.dashboard');
    Route::get('/students', [AdminStudentController::class, 'index'])->name('admin.students.index');
    Route::get('/students/{user}', [AdminStudentController::class, 'show'])->name('admin.students.show');
    Route::patch('/students/{user}/suspend', [AdminStudentController::class, 'suspend'])->name('admin.students.suspend');
    Route::patch('/students/{user}/activate', [AdminStudentController::class, 'activate'])->name('admin.students.activate');
    Route::get('/skills', [AdminSkillController::class, 'index'])->name('admin.skills.index');
    Route::get('/skills/create', [AdminSkillController::class, 'create'])->name('admin.skills.create');
    Route::post('/skills', [AdminSkillController::class, 'store'])->name('admin.skills.store');
    Route::get('/skills/{skill}/edit', [AdminSkillController::class, 'edit'])->name('admin.skills.edit');
    Route::match(['put', 'patch'], '/skills/{skill}', [AdminSkillController::class, 'update'])->name('admin.skills.update');
    Route::delete('/skills/{skill}', [AdminSkillController::class, 'destroy'])->name('admin.skills.destroy');
    Route::get('/exchange-requests', [AdminExchangeRequestController::class, 'index'])->name('admin.exchange-requests.index');
    Route::get('/exchange-requests/{exchangeRequest}', [AdminExchangeRequestController::class, 'show'])->name('admin.exchange-requests.show');
});
