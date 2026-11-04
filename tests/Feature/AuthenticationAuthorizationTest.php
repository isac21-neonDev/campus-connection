<?php

use App\Models\Course;
use App\Models\CourseRegistration;
use App\Models\Student;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(RoleAndPermissionSeeder::class);
});

test('the role seeder grants only the expected role permissions', function () {
    expect(Role::findByName('administrator')->hasPermissionTo('users.manage'))->toBeTrue()
        ->and(Role::findByName('lecturer')->hasPermissionTo('students.view'))->toBeTrue()
        ->and(Role::findByName('lecturer')->hasPermissionTo('courses.manage'))->toBeFalse()
        ->and(Role::findByName('student')->hasPermissionTo('courses.view'))->toBeTrue()
        ->and(Role::findByName('student')->hasPermissionTo('registrations.manage'))->toBeFalse();
});

test('login authenticates with a session and logout invalidates it', function () {
    $user = User::factory()->create([
        'name' => 'Taylor Login',
        'email' => 'taylor.login@example.test',
        'role' => 'student',
    ]);
    $user->assignRole('student');

    $this->post(route('login.store'), [
        'email' => $user->email,
        'password' => 'password',
    ])->assertRedirect(route('dashboard'));

    $this->assertAuthenticatedAs($user);
    $this->get('/portal/dashboard.html')
        ->assertOk()
        ->assertSee('Taylor Login')
        ->assertSee('taylor.login@example.test');

    $this->post(route('logout'))->assertRedirect(route('login'));
    $this->assertGuest();
});

test('each signed-in role sees its own identity on its dashboard and profile', function () {
    $accounts = [
        ['role' => 'student', 'dashboard' => '/portal/dashboard.html', 'name' => 'Ava Student'],
        ['role' => 'lecturer', 'dashboard' => '/portal/staff/dashboard.html', 'name' => 'Lee Lecturer'],
        ['role' => 'administrator', 'dashboard' => '/portal/admin/dashboard.html', 'name' => 'Alex Admin'],
    ];

    foreach ($accounts as $account) {
        $user = User::factory()->create([
            'name' => $account['name'],
            'email' => strtolower(str_replace(' ', '.', $account['name'])) . '@example.test',
            'role' => $account['role'],
        ]);
        $user->assignRole($account['role']);

        $this->actingAs($user)
            ->get($account['dashboard'])
            ->assertOk()
            ->assertSee($account['name'])
            ->assertSee($user->email)
            ->assertSee('Good afternoon, ' . explode(' ', $account['name'])[0]);

        $this->get('/portal/profile.html')
            ->assertOk()
            ->assertSee($account['name'])
            ->assertSee($user->email)
            ->assertSee(ucfirst($account['role']));

        auth()->logout();
    }
});

test('public registration links only an existing student and cannot choose a privileged role', function () {
    $student = Student::create([
        'student_number' => 'STU100',
        'first_name' => 'Sam',
        'last_name' => 'Student',
        'email' => 'sam.student@example.test',
    ]);

    $this->post(route('register.store'), [
        'name' => 'Sam Student',
        'email' => $student->email,
        'student_number' => $student->student_number,
        'password' => 'StrongPass123',
        'password_confirmation' => 'StrongPass123',
        'role' => 'administrator',
    ])->assertRedirect(route('dashboard'));

    $user = User::where('email', $student->email)->firstOrFail();

    expect($user->hasRole('student'))->toBeTrue()
        ->and($user->hasRole('administrator'))->toBeFalse()
        ->and($student->fresh()->user_id)->toBe($user->id);

    $this->get('/portal/dashboard.html')
        ->assertOk()
        ->assertSee('Sam Student')
        ->assertSee('sam.student@example.test');
});

test('registration creates and links a student record for a new student', function () {
    $this->from(route('register'))->post(route('register.store'), [
        'name' => 'New Student',
        'email' => 'new.student@example.test',
        'student_number' => 'STU300',
        'password' => 'StrongPass123',
        'password_confirmation' => 'StrongPass123',
    ])->assertRedirect(route('dashboard'));

    $user = User::where('email', 'new.student@example.test')->firstOrFail();
    $student = Student::where('student_number', 'STU300')->firstOrFail();

    expect($user->hasRole('student'))->toBeTrue()
        ->and($student->user_id)->toBe($user->id)
        ->and($student->first_name)->toBe('New')
        ->and($student->last_name)->toBe('Student');

    $this->get('/portal/dashboard.html')
        ->assertOk()
        ->assertSee('New Student')
        ->assertSee('new.student@example.test');
});

test('registration rejects a mismatched existing student record', function () {
    Student::create([
        'student_number' => 'STU301',
        'first_name' => 'Existing',
        'last_name' => 'Student',
        'email' => 'existing.student@example.test',
    ]);

    $this->from(route('register'))->post(route('register.store'), [
        'name' => 'Existing Student',
        'email' => 'existing.student@example.test',
        'student_number' => 'STU999',
        'password' => 'StrongPass123',
        'password_confirmation' => 'StrongPass123',
    ])->assertRedirect(route('register'))
        ->assertSessionHasErrors('student_number');

    expect(User::where('email', 'existing.student@example.test')->exists())->toBeFalse();
});

test('students see available courses and only their own registrations and records', function () {
    $studentUser = User::factory()->create(['role' => 'student']);
    $studentUser->assignRole('student');
    $studentUser->givePermissionTo('students.view');

    $otherUser = User::factory()->create(['role' => 'student']);
    $otherUser->assignRole('student');

    $student = Student::create([
        'user_id' => $studentUser->id,
        'student_number' => 'STU101',
        'first_name' => 'Ava',
        'last_name' => 'Own',
        'email' => $studentUser->email,
    ]);
    $otherStudent = Student::create([
        'user_id' => $otherUser->id,
        'student_number' => 'STU102',
        'first_name' => 'Omar',
        'last_name' => 'Other',
        'email' => $otherUser->email,
    ]);

    $course = Course::create(['code' => 'OWN101', 'title' => 'Own Course']);
    $otherCourse = Course::create(['code' => 'OTH101', 'title' => 'Other Course']);

    $ownRegistration = CourseRegistration::create([
        'student_id' => $student->id,
        'course_id' => $course->id,
    ]);
    $otherRegistration = CourseRegistration::create([
        'student_id' => $otherStudent->id,
        'course_id' => $otherCourse->id,
    ]);

    $this->actingAs($studentUser)
        ->get(route('courses.index'))
        ->assertOk()
        ->assertSee('Own Course')
        ->assertSee('Other Course');

    $this->get(route('registrations.index'))
        ->assertOk()
        ->assertSee('Own Course')
        ->assertDontSee('Other Course');

    $this->get(route('registrations.show', $otherRegistration))->assertForbidden();
    $this->get(route('students.show', $otherStudent))->assertForbidden();
    $this->get('/portal/admin/dashboard.html')->assertForbidden();
    $this->post(route('courses.store'), ['code' => 'NOPE1', 'title' => 'No access'])->assertForbidden();

    $this->get(route('registrations.show', $ownRegistration))->assertOk();
});

test('lecturers see assigned courses and students registered in those courses only', function () {
    $lecturer = User::factory()->create(['role' => 'lecturer']);
    $lecturer->assignRole('lecturer');
    $otherLecturer = User::factory()->create(['role' => 'lecturer']);
    $otherLecturer->assignRole('lecturer');

    $assignedCourse = Course::create([
        'code' => 'LEC101',
        'title' => 'Assigned Course',
        'lecturer_id' => $lecturer->id,
    ]);
    Course::create([
        'code' => 'LEC102',
        'title' => 'Other Lecturer Course',
        'lecturer_id' => $otherLecturer->id,
    ]);

    $assignedStudent = Student::create([
        'student_number' => 'STU201',
        'first_name' => 'Registered',
        'last_name' => 'Student',
        'email' => 'registered@example.test',
    ]);
    Student::create([
        'student_number' => 'STU202',
        'first_name' => 'Unrelated',
        'last_name' => 'Student',
        'email' => 'unrelated@example.test',
    ]);
    CourseRegistration::create([
        'student_id' => $assignedStudent->id,
        'course_id' => $assignedCourse->id,
    ]);

    $this->actingAs($lecturer)
        ->get(route('courses.index'))
        ->assertOk()
        ->assertSee('Assigned Course')
        ->assertDontSee('Other Lecturer Course');

    $this->get(route('students.index'))
        ->assertOk()
        ->assertSee('Registered Student')
        ->assertDontSee('Unrelated Student');

    $this->get(route('courses.create'))->assertForbidden();
});

test('administrators manage records and can update another user role', function () {
    $administrator = User::factory()->create(['role' => 'administrator']);
    $administrator->assignRole('administrator');

    $student = User::factory()->create(['role' => 'student']);
    $student->assignRole('student');

    $lecturer = User::factory()->create(['role' => 'lecturer']);
    $lecturer->assignRole('lecturer');

    $this->actingAs($administrator)
        ->post(route('courses.store'), [
            'code' => 'ADM101',
            'title' => 'Admin Course',
            'lecturer_id' => $lecturer->id,
        ])
        ->assertRedirect(route('courses.index'));

    expect(Course::where('code', 'ADM101')->value('lecturer_id'))->toBe($lecturer->id);

    $this->from(route('courses.create'))
        ->post(route('courses.store'), [
            'code' => 'ADM102',
            'title' => 'Invalid Lecturer Course',
            'lecturer_id' => $student->id,
        ])
        ->assertRedirect(route('courses.create'))
        ->assertSessionHasErrors('lecturer_id');

    $this->patch(route('admin.users.role', $student), ['role' => 'lecturer'])
        ->assertRedirect(route('admin.users.index'));

    expect($student->fresh()->hasRole('lecturer'))->toBeTrue()
        ->and($student->fresh()->role)->toBe('lecturer');
});

test('the last administrator cannot demote themselves', function () {
    $administrator = User::factory()->create(['role' => 'administrator']);
    $administrator->assignRole('administrator');

    $this->actingAs($administrator)
        ->patch(route('admin.users.role', $administrator), ['role' => 'student'])
        ->assertForbidden();

    expect($administrator->fresh()->hasRole('administrator'))->toBeTrue();
});