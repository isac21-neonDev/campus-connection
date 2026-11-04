@extends('layouts.app')

@section('title', 'Courses')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>Courses</h1>
        @if($canManageCourses)
            <a href="{{ route('courses.create') }}" class="btn btn-primary">Add Course</a>
        @endif
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            @if($courses->isEmpty())
                <p class="text-muted mb-0">No courses found.</p>
            @else
                <div class="table-responsive">
                    <table class="table table-striped mb-0 align-middle">
                        <thead>
                            <tr>
                                <th>Code</th>
                                <th>Title</th>
                                <th>Credits</th>
                                <th>Description</th>
                                @if($canManageCourses)
                                    <th>Actions</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($courses as $course)
                                <tr>
                                    <td>{{ $course->code }}</td>
                                    <td>{{ $course->title }}</td>
                                    <td>{{ $course->credit_units ?? '—' }}</td>
                                    <td>{{ $course->description ?? '—' }}</td>
                                    @if($canManageCourses)
                                        <td>
                                            <div class="d-flex gap-2">
                                                <a href="{{ route('courses.edit', $course) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                                <form action="{{ route('courses.destroy', $course) }}" method="POST" onsubmit="return confirm('Delete this course?');">
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
