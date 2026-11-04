<?php

use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('the portal homepage and nested pages are served from Blade views', function () {
    $this->get('/')
        ->assertOk()
        ->assertSee('/portal/login.html', false);

    $this->get('/portal/login.html')->assertOk();
    $this->get('/portal/admin/users/index.html')->assertRedirect(route('login'));

    $this->seed(RoleAndPermissionSeeder::class);
    $administrator = User::factory()->create(['role' => 'administrator']);
    $administrator->assignRole('administrator');

    $this->actingAs($administrator)
        ->get('/portal/admin/users/index.html')
        ->assertOk()
        ->assertSee('User Access')
        ->assertSee('Update access')
        ->assertSee(route('admin.users.role', $administrator), false);
});

test('unknown portal pages return a not found response', function () {
    $this->get('/portal/not-a-page.html')->assertRedirect(route('login'));

    $this->actingAs(User::factory()->create())
        ->get('/portal/not-a-page.html')
        ->assertNotFound();
});
