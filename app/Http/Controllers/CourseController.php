<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class CourseController extends Controller
{
    public function index(): View
    {
        return view('courses.index', [
            'courses' => $this->visibleCourses(),
            'canManageCourses' => $this->canManageCourses(),
        ]);
    }

    public function create(): View
    {
        return view('courses.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:20', 'unique:courses,code'],
            'title' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string'],
            'credit_units' => ['nullable', 'integer', 'min:1', 'max:12'],
        ]);

        Course::create($validated);

        return Redirect::route('courses.index')->with('success', 'Course created successfully.');
    }

    public function show(Course $course): View
    {
        return view('courses.show', compact('course'));
    }

    public function edit(Course $course): View
    {
        return view('courses.edit', compact('course'));
    }

    public function update(Request $request, Course $course): RedirectResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:20', 'unique:courses,code,' . $course->id],
            'title' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string'],
            'credit_units' => ['nullable', 'integer', 'min:1', 'max:12'],
        ]);

        $course->update($validated);

        return Redirect::route('courses.index')->with('success', 'Course updated successfully.');
    }

    public function destroy(Course $course): RedirectResponse
    {
        $course->delete();

        return Redirect::route('courses.index')->with('success', 'Course deleted successfully.');
    }

    protected function visibleCourses()
    {
        $role = $this->currentRole();

        if ($role === 'student' && auth()->check()) {
            $student = Student::where('email', auth()->user()->email)->first();

            return $student ? $student->courses()->orderBy('title')->get() : collect();
        }

        return Course::orderBy('title')->get();
    }

    protected function canManageCourses(): bool
    {
        return in_array($this->currentRole(), ['admin', 'lecturer'], true);
    }

    protected function currentRole(): string
    {
        return auth()->user()->role ?? 'admin';
    }
}
