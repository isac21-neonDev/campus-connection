<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class StudentController extends Controller
{
    public function index(): View
    {
        return view('students.index', [
            'students' => $this->visibleStudents(),
            'canManageStudents' => $this->canManageStudents(),
        ]);
    }

    public function create(): View
    {
        return view('students.create');
    }

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

        return Redirect::route('students.index')->with('success', 'Student created successfully.');
    }

    public function show(Student $student): View
    {
        return view('students.show', compact('student'));
    }

    public function edit(Student $student): View
    {
        return view('students.edit', compact('student'));
    }

    public function update(Request $request, Student $student): RedirectResponse
    {
        $validated = $request->validate([
            'student_number' => ['required', 'string', 'max:30', 'unique:students,student_number,' . $student->id],
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255', 'unique:students,email,' . $student->id],
            'programme' => ['nullable', 'string', 'max:150'],
            'year_of_study' => ['nullable', 'integer', 'min:1', 'max:8'],
        ]);

        $student->update($validated);

        return Redirect::route('students.index')->with('success', 'Student updated successfully.');
    }

    public function destroy(Student $student): RedirectResponse
    {
        $student->delete();

        return Redirect::route('students.index')->with('success', 'Student deleted successfully.');
    }

    public function visibleStudents()
    {
        $user = auth()->user();
        $query = Student::query();

        if ($user->hasRole('lecturer')) {
            $query->whereHas('courses', fn ($courses) => $courses->where('lecturer_id', $user->id));
        } elseif ($user->hasRole('student')) {
            $query->where('user_id', $user->id);
        }

        return $query->orderBy('last_name')->get();
    }

    public function canManageStudents(): bool
    {
        return auth()->user()->can('students.manage');
    }
}
