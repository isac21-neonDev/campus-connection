<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class StudentController extends Controller
{
    /**
     * Display all visible students.
     */
    public function index(): View
    {
        return view('students.index', [
            'students' => $this->visibleStudents(),
            'canManageStudents' => $this->canManageStudents(),
        ]);
    }

    /**
     * Show the student creation form.
     */
    public function create(): View
    {
        return view('students.create');
    }

    /**
     * Store a new student.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'student_number' => ['required', 'string', 'max:30', 'unique:students,student_number'],
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255', 'unique:students,email'],
            'programme' => ['nullable', 'string', 'max:150'],
            'year_of_study' => ['nullable', 'integer', 'min:1', 'max:8'],
        ]);

        Student::create($validated);

        return Redirect::route('students.index')
            ->with('success', 'Student created successfully.');
    }

    /**
     * Display a specific student and their courses.
     */
    public function show(Student $student): View
    {
        $user = auth()->user();

        $courses = $student->courses()
            ->when(
                $user->hasRole('lecturer'),
                fn ($q) => $q->where('lecturer_id', $user->id)
            )
            ->with('lecturer')
            ->orderBy('title')
            ->get();

        return view('students.show', compact('student', 'courses'));
    }

    /**
     * Show the student editing form.
     */
    public function edit(Student $student): View
    {
        return view('students.edit', compact('student'));
    }

    /**
     * Update a student.
     */
    public function update(Request $request, Student $student): RedirectResponse
    {
        $validated = $request->validate([
            'student_number' => [
                'required',
                'string',
                'max:30',
                'unique:students,student_number,' . $student->id
            ],
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => [
                'required',
                'email',
                'max:255',
                'unique:students,email,' . $student->id
            ],
            'programme' => ['nullable', 'string', 'max:150'],
            'year_of_study' => ['nullable', 'integer', 'min:1', 'max:8'],
        ]);

        $student->update($validated);

        return Redirect::route('students.index')
            ->with('success', 'Student updated successfully.');
    }

    /**
     * Delete a student.
     */
    public function destroy(Student $student): RedirectResponse
    {
        $student->delete();

        return Redirect::route('students.index')
            ->with('success', 'Student deleted successfully.');
    }

    /**
     * Get students visible to the authenticated user.
     */
    public function visibleStudents()
    {
        $user = auth()->user();

        $query = Student::query();

        if ($user->hasRole('lecturer')) {
            $query->whereHas('courses', function ($courses) use ($user) {
                $courses->where('lecturer_id', $user->id);
            });
        } elseif ($user->hasRole('student')) {
            $query->where('user_id', $user->id);
        }

        return $query->orderBy('last_name')->get();
    }

    /**
     * Check whether the authenticated user can manage students.
     */
    public function canManageStudents(): bool
    {
        return auth()->user()->can('students.manage');
    }
}
