<?php

use App\Models\Course;
use App\Models\CourseRegistration;
use App\Models\Student;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('database seeder can run repeatedly without creating duplicate records', function () {
    $this->seed(DatabaseSeeder::class);
    $this->seed(DatabaseSeeder::class);

    expect(User::count())->toBe(7)
        ->and(Student::count())->toBe(5)
        ->and(Course::count())->toBe(4)
        ->and(CourseRegistration::count())->toBe(5)
        ->and(User::where('email', 'isaac@gmail.com')->first()->hasRole('student'))->toBeTrue();
});
