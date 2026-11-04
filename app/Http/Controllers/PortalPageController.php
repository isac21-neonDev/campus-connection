<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\View as ViewFacade;
use Illuminate\View\View;

class PortalPageController extends Controller
{
    public function protectedPage(?string $page = null): View
    {
        return $this->render($page ?? 'dashboard.html');
    }

    public function staff(?string $page = null): View
    {
        return $this->render('staff/' . ($page ?? 'dashboard.html'));
    }

    public function administrator(?string $page = null): View
    {
        return $this->render('admin/' . ($page ?? 'dashboard.html'));
    }

    private function render(string $page): View
    {
        abort_unless(
            preg_match('/\A[a-zA-Z0-9._\/-]+\.html\z/', $page) === 1
                && ! str_contains($page, '..'),
            404,
        );

        $view = 'campus-service-portal.' . str_replace('/', '.', substr($page, 0, -5));

        abort_unless(ViewFacade::exists($view), 404);

        return view($view);
    }
}