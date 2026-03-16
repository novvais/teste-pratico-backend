<?php

use App\Models\Gateway;
use App\Models\User;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;

pest()->use(RefreshDatabase::class);

test('ADMIN can update priority', function () {
    $gateway = Gateway::factory()->create([
        'name' => 'Stripe',
        'is_active' => 1,
        'priority' => 1
    ]);

    $role = Role::factory()->create([
        'name' => 'ADMIN',
        'description' => 'Can do anything.'
    ]);

    $user = User::factory()->create([
        'email' => 'eduardo@gmail.com',
        'password' => '12345678'
    ]);

    $user->roles()->attach($role->id);

    $response = $this->actingAs($user)->patchJson("/api/gateway/{$gateway->id}", ['priority' => 2]);

    $response->assertStatus(200);
});

test('ADMIN can activate an inactive gateway', function () {
    $gateway = Gateway::factory()->create([
        'name' => 'Stripe',
        'is_active' => 0,
        'priority' => 1
    ]);

    $role = Role::factory()->create([
        'name' => 'ADMIN',
        'description' => 'Can do anything.'
    ]);

    $user = User::factory()->create([
        'email' => 'eduardo@gmail.com',
        'password' => '12345678'
    ]);

    $user->roles()->attach($role->id);

    $response = $this->actingAs($user)->patchJson("/api/gateway/{$gateway->id}", ['is_active' => 1]);

    $response->assertStatus(200);
});

test('ADMIN can desactivate an active gateway', function () {
    $gateway = Gateway::factory()->create([
        'name' => 'Stripe',
        'is_active' => 1,
        'priority' => 1
    ]);

    $role = Role::factory()->create([
        'name' => 'ADMIN',
        'description' => 'Can do anything.'
    ]);

    $user = User::factory()->create([
        'email' => 'eduardo@gmail.com',
        'password' => '12345678'
    ]);

    $user->roles()->attach($role->id);

    $response = $this->actingAs($user)->patchJson("/api/gateway/{$gateway->id}", ['is_active' => 0]);

    $response->assertStatus(200);
});
