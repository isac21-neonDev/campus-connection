<?php

use App\Http\Controllers\CourseController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\StudentController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('campus-service-portal.index');
});

Route::resource('students', StudentController::class);
Route::resource('courses', CourseController::class);
Route::resource('registrations', RegistrationController::class);

Route::get('/portal/{page?}', function (?string $page = null) {
    $page ??= 'index.html';

    abort_unless(
        preg_match('/\A[a-zA-Z0-9._\/-]+\.html\z/', $page) === 1
            && ! str_contains($page, '..'),
        404,
    );

    $view = 'campus-service-portal.' . str_replace(
        '/',
        '.',
        substr($page, 0, -5),
    );

    abort_unless(view()->exists($view), 404);

    return view($view);
})->where('page', '.*');
