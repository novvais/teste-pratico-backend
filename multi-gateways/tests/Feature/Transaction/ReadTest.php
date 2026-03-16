<?php

use App\Models\User;
use App\Models\Client;
use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Foundation\Testing\RefreshDatabase;

pest()->use(RefreshDatabase::class);

test('can list all transactions', function () {
    $user = User::factory()->create([
        'email' => 'admin@betalent.tech',
        'password' => '12345678'
    ]);

    $client = Client::create([
        'name' => 'Eduardo',
        'email' => 'eduardo@betalent.tech'
    ]);

    Transaction::create([
        'client_id' => $client->id,
        'amount' => 1000,
        'status' => 'paid'
    ]);

    Transaction::create([
        'client_id' => $client->id,
        'amount' => 2000,
        'status' => 'pending'
    ]);

    $response = $this->actingAs($user)->getJson('/api/transactions');

    $response->assertStatus(200);

    $response->assertJsonCount(2);
});

test('can show a specific transaction', function () {
    $user = User::factory()->create([
        'email' => 'admin@betalent.tech',
        'password' => '12345678'
    ]);

    $client = Client::create([
        'name' => 'Eduardo',
        'email' => 'eduardo@betalent.tech'
    ]);

    $product = Product::factory()->create([
        'name' => 'Celular',
        'amount' => 12000,
        'stock' => 10
    ]);

    $transaction = Transaction::create([
        'client_id' => $client->id,
        'amount' => 12000,
        'status' => 'paid'
    ]);

    $transaction->products()->attach($product->id, [
        'quantity' => 1,
        'unit_amount' => $product->amount
    ]);

    $response = $this->actingAs($user)->getJson("/api/transactions/{$transaction->id}");

    $response->assertStatus(200);

    $response->assertJsonFragment(['status' => 'paid']);
});