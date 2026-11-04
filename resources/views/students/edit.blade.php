@extends('layouts.app')

@section('title', 'Edit Student')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Edit Student</h1>
        <a href="{{ route('students.index') }}" class="btn btn-outline-secondary">Back</a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('students.update', $student) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Student Number</label>
                        <input type="text" name="student_number" class="form-control" value="{{ old('student_number', $student->student_number) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email', $student->email) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">First Name</label>
                        <input type="text" name="first_name" class="form-control" value="{{ old('first_name', $student->first_name) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Last Name</label>
                        <input type="text" name="last_name" class="form-control" value="{{ old('last_name', $student->last_name) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Programme</label>
                        <input type="text" name="programme" class="form-control" value="{{ old('programme', $student->programme) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Year of Study</label>
                        <input type="number" name="year_of_study" min="1" max="8" class="form-control" value="{{ old('year_of_study', $student->year_of_study) }}">
                    </div>
                </div>
                <button type="submit" class="btn btn-primary mt-3">Update Student</button>
            </form>
        </div>
    </div>
@endsection
