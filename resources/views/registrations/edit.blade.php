@extends('layouts.app')

@section('title', 'Edit Registration')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Edit Registration</h1>
        <a href="{{ route('registrations.index') }}" class="btn btn-outline-secondary">Back</a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('registrations.update', $registration) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Student</label>
                        <select name="student_id" class="form-select" required>
                            <option value="">Select student</option>
                            @foreach($students as $student)
                                <option value="{{ $student->id }}" {{ old('student_id', $registration->student_id) == $student->id ? 'selected' : '' }}>
                                    {{ $student->first_name }} {{ $student->last_name }} ({{ $student->student_number }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Course</label>
                        <select name="course_id" class="form-select" required>
                            <option value="">Select course</option>
                            @foreach($courses as $course)
                                <option value="{{ $course->id }}" {{ old('course_id', $registration->course_id) == $course->id ? 'selected' : '' }}>
                                    {{ $course->title }} ({{ $course->code }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary mt-3">Update Registration</button>
            </form>
        </div>
    </div>
@endsection
