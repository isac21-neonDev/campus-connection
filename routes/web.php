<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('campus-service-portal.index');
});

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
