<?php

use App\Models\User;
use App\Models\Client;
use App\Models\Transaction;
use Illuminate\Foundation\Testing\RefreshDatabase;

pest()->use(RefreshDatabase::class);

test('can list all clients', function () {
    $user = User::factory()->create([
        'email' => 'admin@betalent.tech',
        'password' => '12345678'
    ]);

    Client::factory()->count(3)->create();

    $response = $this->actingAs($user)->getJson('/api/clients');

    $response->assertStatus(200);
    
    $response->assertJsonCount(3);
});

test('can show a specific client with transactions', function () {
    $user = User::factory()->create([
        'email' => 'admin@betalent.tech',
        'password' => '12345678'
    ]);

    $client = Client::factory()->create();

    Transaction::create([
        'client_id' => $client->id,
        'amount' => 5000,
        'status' => 'paid'
    ]);

    $response = $this->actingAs($user)->getJson("/api/clients/{$client->id}");

    $response->assertStatus(200);
});