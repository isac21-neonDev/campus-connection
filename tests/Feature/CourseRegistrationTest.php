<?php

use App\Models\Course;
use App\Models\CourseRegistration;
use App\Models\Student;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('students and courses expose their registrations and many-to-many relationship', function () {
    $student = Student::create([
        'student_number' => 'STU001',
        'first_name' => 'Ada',
        'last_name' => 'Lovelace',
        'email' => 'ada@example.com',
    ]);

    $course = Course::create([
        'code' => 'BCS101',
        'title' => 'Web Application Development',
    ]);

    CourseRegistration::create([
        'student_id' => $student->id,
        'course_id' => $course->id,
    ]);

    expect($student->courseRegistrations)->toHaveCount(1)
        ->and($student->courses->modelKeys())->toBe([$course->id])
        ->and($course->courseRegistrations->first()->student->is($student))->toBeTrue()
        ->and($course->students->modelKeys())->toBe([$student->id]);
});

test('a student cannot register for the same course more than once', function () {
    $student = Student::create([
        'student_number' => 'STU002',
        'first_name' => 'Grace',
        'last_name' => 'Hopper',
        'email' => 'grace@example.com',
    ]);

    $course = Course::create([
        'code' => 'BCS102',
        'title' => 'Database Systems',
    ]);

    CourseRegistration::create([
        'student_id' => $student->id,
        'course_id' => $course->id,
    ]);

    expect(fn () => CourseRegistration::create([
        'student_id' => $student->id,
        'course_id' => $course->id,
    ]))->toThrow(QueryException::class);
});

test('a registration must reference an existing student and course', function () {
    $student = Student::create([
        'student_number' => 'STU003',
        'first_name' => 'Katherine',
        'last_name' => 'Johnson',
        'email' => 'katherine@example.com',
    ]);

    expect(fn () => CourseRegistration::create([
        'student_id' => $student->id,
        'course_id' => 999,
    ]))->toThrow(QueryException::class);
});
