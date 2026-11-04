<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('campus-service-portal.register');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:255'],
            'student_number' => ['required', 'string', 'max:30'],
            'email' => [
                'required',
                'email',
                'max:255',
                'unique:users,email',
            ],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $user = DB::transaction(function () use ($validated): User {
            $student = Student::where('email', $validated['email'])
                ->lockForUpdate()
                ->first();

            if ($student && $student->student_number !== $validated['student_number']) {
                throw ValidationException::withMessages([
                    'student_number' => 'That student number does not match the campus record for this email.',
                ]);
            }

            if ($student?->user_id) {
                throw ValidationException::withMessages([
                    'email' => 'An account already exists for this student record. Please sign in instead.',
                ]);
            }

            if (! $student && Student::where('student_number', $validated['student_number'])->exists()) {
                throw ValidationException::withMessages([
                    'student_number' => 'That student number is already registered with a different email.',
                ]);
            }

            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => $validated['password'],
                'role' => 'student',
            ]);

            $user->syncRoles('student');

            if (! $student) {
                $nameParts = preg_split('/\s+/', trim($validated['name']), 2);
                $student = new Student([
                    'student_number' => $validated['student_number'],
                    'first_name' => $nameParts[0],
                    'last_name' => $nameParts[1] ?? $nameParts[0],
                    'email' => $validated['email'],
                ]);
            }

            $student->user()->associate($user)->save();

            return $user;
        });

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('dashboard');
    }
}