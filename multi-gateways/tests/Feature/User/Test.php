<?php

use App\Models\User;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;

pest()->use(RefreshDatabase::class);

test('can list all users', function () {
    $role = Role::factory()->create(['name' => 'ADMIN']);
    $admin = User::factory()->create();
    $admin->roles()->attach($role->id);

    User::factory()->count(2)->create();

    $response = $this->actingAs($admin)->getJson('/api/users');

    $response->assertStatus(200);
    
    $response->assertJsonCount(3);
});

test('can create a new user', function () {
    $role = Role::factory()->create(['name' => 'ADMIN']);
    $admin = User::factory()->create();
    $admin->roles()->attach($role->id);

    $response = $this->actingAs($admin)->postJson('/api/users', [
        'name' => 'Novo Usuario',
        'email' => 'novo@betalent.tech',
        'password' => '12345678',
        'role' => 'MANAGER'
    ]);

    $response->assertStatus(201);
    
    $this->assertDatabaseHas('users', ['email' => 'novo@betalent.tech']);
});

test('can update a user', function () {
    $role = Role::factory()->create(['name' => 'ADMIN']);
    $admin = User::factory()->create();
    $admin->roles()->attach($role->id);

    $user = User::factory()->create();

    $response = $this->actingAs($admin)->patchJson("/api/users/{$user->id}", [
        'email' => 'atualizado@betalent.tech'
    ]);

    $response->assertStatus(200);
    
    $this->assertDatabaseHas('users', ['email' => 'atualizado@betalent.tech']);
});

test('can delete a user', function () {
    $role = Role::factory()->create(['name' => 'ADMIN']);
    $admin = User::factory()->create();
    $admin->roles()->attach($role->id);

    $user = User::factory()->create(['password' => '12345678']);

    $response = $this->actingAs($admin)->deleteJson("/api/users/{$user->id}");

    $response->assertStatus(204);
    
    $this->assertSoftDeleted('users', ['id' => $user->id]);
});