<?php

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
 
pest()->use(RefreshDatabase::class);    

test('ADMIN can access create products route', function () {
    $role = Role::factory()->create([
        'name' => 'ADMIN',
        'description' => 'Can do anything.'
    ]);

    $user = User::factory()->create([
        'email' => 'eduardo@gmail.com',
        'password' => '12345678',
    ]);

    $user->roles()->attach($role->id);

    $response = $this->actingAs($user)->postJson('/api/products', []);

    $response->assertStatus(422);
});

test('USER cannot create products', function () {
    $role = Role::factory()->create([
        'name' => 'USER',
        'description' => 'Only view.'
    ]);

    $user = User::factory()->create([
        'email' => 'eduardo@gmail.com',
        'password' => '12345678',
    ]);

    $user->roles()->attach($role->id);

    $response = $this->actingAs($user)->postJson('/api/products', ['name' => 'Celular', 'amount' => 12000, 'stock' => 10]);

    $response->assertStatus(403);
});

test('ADMIN can successfully create a product', function () {
    $role = Role::factory()->create([
        'name' => 'ADMIN',
        'description' => 'Can do anything.'
    ]);

    $user = User::factory()->create([
        'email' => 'eduardo@gmail.com',
        'password' => '12345678',
    ]);

    $user->roles()->attach($role->id);

    $response = $this->actingAs($user)->postJson('/api/products', ['name' => 'Celular', 'amount' => 12000, 'stock' => 10]);

    $response->assertStatus(201);

    $this->assertDatabaseHas('products', ['name' => 'Celular']);
});