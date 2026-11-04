@extends('layouts.app')

@section('title', 'Students')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Students</h1>
        @if($canManageStudents)
            <a href="{{ route('students.create') }}" class="btn btn-primary">Add Student</a>
        @endif
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            @if($students->isEmpty())
                <p class="text-muted mb-0">No students found.</p>
            @else
                <div class="table-responsive">
                    <table class="table table-striped mb-0 align-middle">
                        <thead>
                            <tr>
                                <th>Student Number</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Programme</th>
                                <th>Year</th>
                                @if($canManageStudents)
                                    <th>Actions</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($students as $student)
                                <tr>
                                    <td>{{ $student->student_number }}</td>
                                    <td>{{ $student->first_name }} {{ $student->last_name }}</td>
                                    <td>{{ $student->email }}</td>
                                    <td>{{ $student->programme ?? '—' }}</td>
                                    <td>{{ $student->year_of_study ?? '—' }}</td>
                                    @if($canManageStudents)
                                        <td>
                                            <div class="d-flex gap-2">
                                                <a href="{{ route('students.edit', $student) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                                <form action="{{ route('students.destroy', $student) }}" method="POST" onsubmit="return confirm('Delete this student?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                                </form>
                                            </div>
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
@endsection
