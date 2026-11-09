<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseRegistration;
use App\Models\Student;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class RegistrationController extends Controller
{
    /**
     * Display all registrations visible to the authenticated user.
     */
    public function index(): View
    {
        return view('registrations.index', [
            'registrations' => $this->visibleRegistrations(),
            'canManageRegistrations' => $this->canManageRegistrations(),
        ]);
    }

    /**
     * Show the registration creation form.
     */
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
            'student_id' => [
                'required',
                'exists:students,id',
            ],

            'course_id' => [
                'required',
                'exists:courses,id',
                Rule::unique('course_registrations', 'course_id')
                    ->where(function ($query) use ($request) {
                        return $query->where(
                            'student_id',
                            $request->input('student_id')
                        );
                    }),
            ],
        ], [
            'course_id.unique' => 'This student is already registered for the selected course.',
        ]);

        try {
            CourseRegistration::create($validated);
        } catch (QueryException $e) {
            return Redirect::back()
                ->withInput()
                ->with('error', 'Registration failed. This student may already be registered for this course.');
        }

        return Redirect::route('registrations.index')
            ->with('success', 'Course registration saved successfully.');
    }

    /**
     * Display a specific registration.
     */
    public function show(CourseRegistration $registration): View
    {
        $registration->load(['student', 'course']);

        return view('registrations.show', compact('registration'));
    }

    /**
     * Show the registration editing form.
     */
    public function edit(CourseRegistration $registration): View
    {
        return view('registrations.edit', [
            'registration' => $registration,
            'students' => Student::orderBy('last_name')->get(),
            'courses' => Course::orderBy('title')->get(),
        ]);
    }

    /**
     * Update an existing registration.
     */
    public function update(
        Request $request,
        CourseRegistration $registration
    ): RedirectResponse {

        $validated = $request->validate([
            'student_id' => [
                'required',
                'exists:students,id',
            ],

            'course_id' => [
                'required',
                'exists:courses,id',
                Rule::unique('course_registrations', 'course_id')
                    ->where(function ($query) use ($request) {
                        return $query->where(
                            'student_id',
                            $request->input('student_id')
                        );
                    })
                    ->ignore($registration->id),
            ],
        ], [
            'course_id.unique' => 'This student is already registered for the selected course.',
        ]);

        $registration->update($validated);

        return Redirect::route('registrations.index')
            ->with('success', 'Registration updated successfully.');
    }

    /**
     * Delete a registration.
     */
    public function destroy(
        CourseRegistration $registration
    ): RedirectResponse {

        $registration->delete();

        return Redirect::route('registrations.index')
            ->with('success', 'Registration deleted successfully.');
    }

    /**
     * Get registrations visible to the authenticated user.
     */
    protected function visibleRegistrations()
    {
        $user = auth()->user();

        $query = CourseRegistration::query()
            ->with(['student', 'course']);

        if ($user->hasRole('lecturer')) {

            $query->whereHas('course', function ($courses) use ($user) {
                $courses->where('lecturer_id', $user->id);
            });

        } elseif ($user->hasRole('student')) {

            $query->whereHas('student', function ($students) use ($user) {
                $students->where('user_id', $user->id);
            });
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    /**
     * Check whether the user can manage registrations.
     */
    protected function canManageRegistrations(): bool
    {
        return auth()->user()->can('registrations.manage');
    }
}
