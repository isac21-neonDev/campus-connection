<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class UserAccessController extends Controller
{
    public function index(): View
    {
        return view('users.index', [
            'users' => User::with('roles')->orderBy('name')->get(),
            'roles' => Role::orderBy('name')->get(),
        ]);
    }

    public function updateRole(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'role' => ['required', Rule::exists('roles', 'name')->where('guard_name', 'web')],
        ]);

        if ($user->is($request->user()) && $validated['role'] !== 'administrator') {
            abort(403);
        }

        $administratorCount = User::role('administrator')->count();
        if ($user->hasRole('administrator')
            && $validated['role'] !== 'administrator'
            && $administratorCount <= 1) {
            return Redirect::back()->withErrors([
                'role' => 'The last administrator cannot be demoted.',
            ]);
        }

        $user->syncRoles($validated['role']);
        $user->forceFill(['role' => $validated['role']])->save();

        return Redirect::route('admin.users.index')->with('success', 'User access updated.');
    }
}