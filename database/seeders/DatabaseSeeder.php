<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\CourseRegistration;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('admin123'),
            'role' => 'admin',
        ]);

        User::factory()->create([
            'name' => 'Student User',
            'email' => 'student@gmail.com',
            'password' => bcrypt('student123'),
            'role' => 'student',
        ]);

        User::factory()->create([
            'name' => 'Lecturer User',
            'email' => 'lecturer@gmail.com',
            'password' => bcrypt('lecturer123'),
            'role' => 'lecturer',
        ]);

        $students = [
            ['student_number' => 'STU001', 'first_name' => 'Isaac', 'last_name' => 'Kasasa', 'email' => 'isaac@gmail.com', 'programme' => 'BSc Computer Science', 'year_of_study' => 2],
            ['student_number' => 'STU002', 'first_name' => 'Moses', 'last_name' => 'Kinene', 'email' => 'moses@gmail.com', 'programme' => 'BSc Software Engineering', 'year_of_study' => 3],
            ['student_number' => 'STU003', 'first_name' => 'Catherine', 'last_name' => 'Kuzara', 'email' => 'catherine@gmail.com', 'programme' => 'BSc Data Science', 'year_of_study' => 1],
            ['student_number' => 'STU004', 'first_name' => 'Issa', 'last_name' => 'Ashaba', 'email' => 'issa@gmail.com', 'programme' => 'BSc Information Systems', 'year_of_study' => 4],
            ['student_number' => 'STU005', 'first_name' => 'Benjamin', 'last_name' => 'Bawiz', 'email' => 'bawiz@gmail.com', 'programme' => 'BSc Computer Engineering', 'year_of_study' => 2],
        ];

        foreach ($students as $student) {
            Student::create($student);
        }

        $courses = [
            ['code' => 'BCS101', 'title' => 'Web Application Development', 'description' => 'Introduction to building and deploying web applications.', 'credit_units' => 3],
            ['code' => 'BCS102', 'title' => 'Database Systems', 'description' => 'Design, implementation, and management of relational databases.', 'credit_units' => 3],
            ['code' => 'BCS103', 'title' => 'Software Engineering', 'description' => 'Principles of software design, testing, and maintenance.', 'credit_units' => 4],
            ['code' => 'BCS104', 'title' => 'Networking Fundamentals', 'description' => 'Core networking concepts, protocols, and communication models.', 'credit_units' => 3],
        ];

        foreach ($courses as $course) {
            Course::create($course);
        }
    }
}
