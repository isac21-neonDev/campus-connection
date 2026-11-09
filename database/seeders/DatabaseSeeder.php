<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\CourseRegistration;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RoleAndPermissionSeeder::class);

        // Create default users
        $users = [
            [
                'name' => 'Admin User',
                'email' => 'admin@gmail.com',
                'password' => 'admin123',
                'role' => 'administrator'
            ],
            [
                'name' => 'Student User',
                'email' => 'isaac@gmail.com',
                'password' => 'student123',
                'role' => 'student'
            ],
            [
                'name' => 'Lecturer User',
                'email' => 'lecturer@gmail.com',
                'password' => 'lecturer123',
                'role' => 'lecturer'
            ],
        ];

        $seededUsers = [];

        foreach ($users as $userData) {
            $user = User::updateOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'password' => Hash::make($userData['password']),
                    'role' => $userData['role'],
                ]
            );

            $user->syncRoles($userData['role']);

            $seededUsers[$userData['role']] = $user;
        }

        // Assign roles to existing users without roles
        User::with('roles')->get()->each(function (User $user): void {
            if ($user->roles->isNotEmpty()) {
                return;
            }

            $role = match ($user->role) {
                'admin', 'administrator' => 'administrator',
                'lecturer' => 'lecturer',
                default => 'student',
            };

            $user->syncRoles($role);
            $user->forceFill(['role' => $role])->save();
        });

        // Create students
        $students = [
            [
                'student_number' => 'STU001',
                'first_name' => 'Isaac',
                'last_name' => 'Kasasa',
                'email' => 'isaac@gmail.com',
                'programme' => 'BSc Computer Science',
                'year_of_study' => 2
            ],
            [
                'student_number' => 'STU002',
                'first_name' => 'Moses',
                'last_name' => 'Kinene',
                'email' => 'moses@gmail.com',
                'programme' => 'BSc Software Engineering',
                'year_of_study' => 3
            ],
            [
                'student_number' => 'STU003',
                'first_name' => 'Catherine',
                'last_name' => 'Kuzara',
                'email' => 'catherine@gmail.com',
                'programme' => 'BSc Data Science',
                'year_of_study' => 1
            ],
            [
                'student_number' => 'STU004',
                'first_name' => 'Issa',
                'last_name' => 'Ashaba',
                'email' => 'issa@gmail.com',
                'programme' => 'BSc Information Systems',
                'year_of_study' => 4
            ],
            [
                'student_number' => 'STU005',
                'first_name' => 'Benjamin',
                'last_name' => 'Bawiz',
                'email' => 'bawiz@gmail.com',
                'programme' => 'BSc Computer Engineering',
                'year_of_study' => 2
            ],
        ];

        foreach ($students as $student) {
            Student::updateOrCreate(
                ['student_number' => $student['student_number']],
                $student
            );
        }

        // Link Isaac to the default student account
        Student::where('student_number', 'STU001')
            ->update(['user_id' => $seededUsers['student']->id]);

        // Create accounts for students without user accounts
        foreach (Student::whereNull('user_id')->get() as $student) {

            $account = User::updateOrCreate(
                ['email' => $student->email],
                [
                    'name' => $student->first_name . ' ' . $student->last_name,
                    'password' => Hash::make('student123'),
                    'role' => 'student',
                ]
            );

            $account->syncRoles('student');

            $student->update([
                'user_id' => $account->id
            ]);
        }

        // Create courses
        $courses = [
            [
                'code' => 'BCS101',
                'title' => 'Web Application Development',
                'description' => 'Introduction to building and deploying web applications.',
                'credit_units' => 3
            ],
            [
                'code' => 'BCS102',
                'title' => 'Database Systems',
                'description' => 'Design, implementation, and management of relational databases.',
                'credit_units' => 3
            ],
            [
                'code' => 'BCS103',
                'title' => 'Software Engineering',
                'description' => 'Principles of software design, testing, and maintenance.',
                'credit_units' => 4
            ],
            [
                'code' => 'BCS104',
                'title' => 'Networking Fundamentals',
                'description' => 'Core networking concepts, protocols, and communication models.',
                'credit_units' => 3
            ],
        ];

        foreach ($courses as $course) {
            Course::updateOrCreate(
                ['code' => $course['code']],
                array_merge($course, [
                    'lecturer_id' => $seededUsers['lecturer']->id
                ])
            );
        }

        // Register students for courses
        $registrations = [
            ['student_number' => 'STU001', 'course_code' => 'BCS101'],
            ['student_number' => 'STU002', 'course_code' => 'BCS101'],
            ['student_number' => 'STU003', 'course_code' => 'BCS102'],
            ['student_number' => 'STU004', 'course_code' => 'BCS103'],
            ['student_number' => 'STU005', 'course_code' => 'BCS104'],
        ];

        foreach ($registrations as $registration) {
            $student = Student::firstWhere(
                'student_number',
                $registration['student_number']
            );

            $course = Course::firstWhere(
                'code',
                $registration['course_code']
            );

            if ($student && $course) {
                CourseRegistration::firstOrCreate([
                    'student_id' => $student->id,
                    'course_id' => $course->id,
                ]);
            }
        }
    }
}
