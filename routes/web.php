<?php

use App\Http\Controllers\CourseController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\PortalPageController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\UserAccessController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'campus-service-portal.index');

Route::get('/login', [AuthenticatedSessionController::class, 'create'])
    ->middleware('guest')
    ->name('login');
Route::post('/login', [AuthenticatedSessionController::class, 'store'])
    ->middleware(['guest', 'throttle:5,1'])
    ->name('login.store');
Route::get('/register', [RegisteredUserController::class, 'create'])
    ->middleware('guest')
    ->name('register');
Route::post('/register', [RegisteredUserController::class, 'store'])
    ->middleware(['guest', 'throttle:5,1'])
    ->name('register.store');
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

Route::get('/dashboard', function () {
    $user = auth()->user();

    return match (true) {
        $user->hasRole('administrator') => redirect('/portal/admin/dashboard.html'),
        $user->hasRole('lecturer') => redirect('/portal/staff/dashboard.html'),
        $user->hasRole('student') => redirect('/portal/dashboard.html'),
        default => abort(403),
    };
})->middleware('auth')->name('dashboard');

Route::view('/portal/index.html', 'campus-service-portal.index');
Route::view('/portal/login.html', 'campus-service-portal.login');
Route::view('/portal/register.html', 'campus-service-portal.register');
Route::view('/portal/contact.html', 'campus-service-portal.contact');
Route::view('/portal/contact-list.html', 'campus-service-portal.contact-list');
Route::view('/portal/forgot-password.html', 'campus-service-portal.forgot-password');
Route::view('/portal/reset-password.html', 'campus-service-portal.reset-password');

Route::view('/portal/dashboard.html', 'campus-service-portal.dashboard')
    ->middleware(['auth', 'role:student']);
Route::get('/portal/staff/{page?}', [PortalPageController::class, 'staff'])
    ->middleware(['auth', 'role:lecturer'])
    ->where('page', '.*');
Route::get('/portal/admin/users/index.html', [UserAccessController::class, 'index'])
    ->middleware(['auth', 'permission:users.view'])
    ->name('portal.admin.users.index');
Route::get('/portal/admin/{page?}', [PortalPageController::class, 'administrator'])
    ->middleware(['auth', 'role:administrator'])
    ->where('page', '.*');
Route::get('/portal/{page?}', [PortalPageController::class, 'protectedPage'])
    ->middleware('auth')
    ->where('page', '.*');

Route::middleware('auth')->group(function () {
    Route::get('/students/create', [StudentController::class, 'create'])
        ->middleware('permission:students.manage')->name('students.create');
    Route::post('/students', [StudentController::class, 'store'])
        ->middleware('permission:students.manage')->name('students.store');
    Route::get('/students/{student}/edit', [StudentController::class, 'edit'])
        ->middleware('permission:students.manage')->name('students.edit');
    Route::match(['put', 'patch'], '/students/{student}', [StudentController::class, 'update'])
        ->middleware('permission:students.manage')->name('students.update');
    Route::delete('/students/{student}', [StudentController::class, 'destroy'])
        ->middleware('permission:students.manage')->name('students.destroy');
    Route::get('/students', [StudentController::class, 'index'])
        ->middleware('permission:students.view')->name('students.index');
    Route::get('/students/{student}', [StudentController::class, 'show'])
        ->middleware(['permission:students.view', 'can:view,student'])->name('students.show');

    Route::get('/courses/create', [CourseController::class, 'create'])
        ->middleware('permission:courses.manage')->name('courses.create');
    Route::post('/courses', [CourseController::class, 'store'])
        ->middleware('permission:courses.manage')->name('courses.store');
    Route::get('/courses/{course}/edit', [CourseController::class, 'edit'])
        ->middleware('permission:courses.manage')->name('courses.edit');
    Route::match(['put', 'patch'], '/courses/{course}', [CourseController::class, 'update'])
        ->middleware('permission:courses.manage')->name('courses.update');
    Route::delete('/courses/{course}', [CourseController::class, 'destroy'])
        ->middleware('permission:courses.manage')->name('courses.destroy');
    Route::get('/courses', [CourseController::class, 'index'])
        ->middleware('permission:courses.view')->name('courses.index');
    Route::get('/courses/{course}', [CourseController::class, 'show'])
        ->middleware(['permission:courses.view', 'can:view,course'])->name('courses.show');

    Route::get('/registrations/create', [RegistrationController::class, 'create'])
        ->middleware('permission:registrations.manage')->name('registrations.create');
    Route::post('/registrations', [RegistrationController::class, 'store'])
        ->middleware('permission:registrations.manage')->name('registrations.store');
    Route::get('/registrations/{registration}/edit', [RegistrationController::class, 'edit'])
        ->middleware('permission:registrations.manage')->name('registrations.edit');
    Route::match(['put', 'patch'], '/registrations/{registration}', [RegistrationController::class, 'update'])
        ->middleware('permission:registrations.manage')->name('registrations.update');
    Route::delete('/registrations/{registration}', [RegistrationController::class, 'destroy'])
        ->middleware('permission:registrations.manage')->name('registrations.destroy');
    Route::get('/registrations', [RegistrationController::class, 'index'])
        ->middleware('permission:registrations.view')->name('registrations.index');
    Route::get('/registrations/{registration}', [RegistrationController::class, 'show'])
        ->middleware(['permission:registrations.view', 'can:view,registration'])->name('registrations.show');

    Route::get('/admin/users', [UserAccessController::class, 'index'])
        ->middleware('permission:users.view')->name('admin.users.index');
    Route::patch('/admin/users/{user}/role', [UserAccessController::class, 'updateRole'])
        ->middleware('permission:users.manage')->name('admin.users.role');
});

