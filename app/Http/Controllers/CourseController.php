<?php
 
namespace App\Http\Controllers;
 
use App\Models\Course;
use App\Models\User;
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
        return view('courses.create', [
            'lecturers' => $this->lecturers(),
        ]);
    }
 
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->rules());
 
        Course::create($validated);
 
        return Redirect::route('courses.index')->with('success', 'Course created successfully.');
    }
 
    public function show(Course $course): View
    {
        $canViewStudents = auth()->user()->can('students.view');
 
        $students = $canViewStudents
            ? $course->students()->orderBy('last_name')->get()
            : collect();
 
        return view('courses.show', compact('course', 'students', 'canViewStudents'));
    }
 
    public function edit(Course $course): View
    {
        return view('courses.edit', [
            'course' => $course,
            'lecturers' => $this->lecturers(),
        ]);
    }
 
    public function update(Request $request, Course $course): RedirectResponse
    {
        $validated = $request->validate($this->rules($course));
 
        $course->update($validated);
 
        return Redirect::route('courses.index')->with('success', 'Course updated successfully.');
    }
 
    public function destroy(Course $course): RedirectResponse
    {
        $course->delete();
 
        return Redirect::route('courses.index')->with('success', 'Course deleted successfully.');
    }
 
    //validate rules
    protected function rules(?Course $course = null): array
    {
        return [
            'code' => [
                'required',
                'string',
                'max:20',
                'unique:courses,code' . ($course ? ',' . $course->id : ''),
            ],
            'title' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string'],
            'credit_units' => ['nullable', 'integer', 'min:1', 'max:12'],
            'lecturer_id' => [
                'nullable',
                'integer',
                'exists:users,id',
                function ($attribute, $value, $fail): void {
                    if ($value && ! User::role('lecturer')->whereKey($value)->exists()) {
                        $fail('The selected user is not a lecturer.');
                    }
                },
            ],
        ];
    }
 
    protected function lecturers()
    {
        return User::role('lecturer')->orderBy('name')->get();
    }
 
    protected function visibleCourses()
    {
        $user = auth()->user();
 
        if ($user->hasRole('lecturer')) {
            return Course::where('lecturer_id', $user->id)->orderBy('title')->get();
        }
 
        return Course::orderBy('title')->get();
    }
 
    protected function canManageCourses(): bool
    {
        return auth()->user()->can('courses.manage');
    }
}