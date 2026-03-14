<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

pest()->use(RefreshDatabase::class);

test('user can login with correct credentials', function () {
    $user = User::factory()->create([
        'email' => 'eduardo@gmail.com',
        'password' => '12345678',
    ]);

    $response = $this->postJson('/api/login', [
        'email' => $user->email,
        'password' => '12345678'
    ]);

    $response->assertStatus(200);

    $response->assertJsonStructure([
        'user',
        'token'
    ]);
});

test('Unauthorized login, password incorrect', function() {
    $user = User::factory()->create([
        'email' => 'eduardo@gmail.com',
        'password' => '12345678'
    ]);

    $response = $this->postJson('/api/login', [
        'email' => $user->email,
        'password' => '12345'
    ]);

    $response->assertStatus(401);

    $response->assertUnauthorized();
});
