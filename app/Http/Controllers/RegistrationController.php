<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseRegistration;
use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class RegistrationController extends Controller
{
    public function index(): View
    {
        return view('registrations.index', [
            'registrations' => $this->visibleRegistrations(),
            'canManageRegistrations' => $this->canManageRegistrations(),
        ]);
    }

    public function create(): View
    {
        return view('registrations.create', [
            'students' => Student::orderBy('last_name')->get(),
            'courses' => Course::orderBy('title')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'student_id' => ['required', 'exists:students,id'],
            'course_id' => ['required', 'exists:courses,id'],
        ]);

        if (CourseRegistration::where('student_id', $validated['student_id'])
            ->where('course_id', $validated['course_id'])
            ->exists()) {
            return Redirect::back()->withErrors([
                'course_id' => 'This student is already registered for the selected course.',
            ]);
        }

        CourseRegistration::create($validated);

        return Redirect::route('registrations.index')->with('success', 'Course registration saved successfully.');
    }

    public function show(CourseRegistration $registration): View
    {
        return view('registrations.show', compact('registration'));
    }

    public function edit(CourseRegistration $registration): View
    {
        return view('registrations.edit', [
            'registration' => $registration,
            'students' => Student::orderBy('last_name')->get(),
            'courses' => Course::orderBy('title')->get(),
        ]);
    }

    public function update(Request $request, CourseRegistration $registration): RedirectResponse
    {
        $validated = $request->validate([
            'student_id' => ['required', 'exists:students,id'],
            'course_id' => ['required', 'exists:courses,id'],
        ]);

        $existing = CourseRegistration::where('student_id', $validated['student_id'])
            ->where('course_id', $validated['course_id'])
            ->whereKeyNot($registration->id)
            ->exists();

        if ($existing) {
            return Redirect::back()->withErrors([
                'course_id' => 'This student is already registered for the selected course.',
            ]);
        }

        $registration->update($validated);

        return Redirect::route('registrations.index')->with('success', 'Registration updated successfully.');
    }

    public function destroy(CourseRegistration $registration): RedirectResponse
    {
        $registration->delete();

        return Redirect::route('registrations.index')->with('success', 'Registration deleted successfully.');
    }

    protected function visibleRegistrations()
    {
        $role = $this->currentRole();

        $query = CourseRegistration::query()->with(['student', 'course']);

        if ($role === 'student' && auth()->check()) {
            $student = Student::where('email', auth()->user()->email)->first();

            if ($student) {
                return $query->where('student_id', $student->id)->orderBy('created_at', 'desc')->get();
            }

            return collect();
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    protected function canManageRegistrations(): bool
    {
        return in_array($this->currentRole(), ['admin', 'lecturer'], true);
    }

    protected function currentRole(): string
    {
        return auth()->user()->role ?? 'admin';
    }
}
