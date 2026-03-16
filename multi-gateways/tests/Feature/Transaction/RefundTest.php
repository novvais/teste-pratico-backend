<?php

use App\Models\User;
use App\Models\Role;
use App\Models\Client;
use App\Models\Card;
use App\Models\Gateway;
use App\Models\Transaction;
use App\Models\TransactionAttempt;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;

pest()->use(RefreshDatabase::class);

test('ADMIN can refund a transaction', function () {
    $role = Role::factory()->create([
        'name' => 'ADMIN',
        'description' => 'Can do anything.'
    ]);

    $user = User::factory()->create([
        'email' => 'admin@betalent.tech',
        'password' => '12345678'
    ]);

    $user->roles()->attach($role->id);

    $client = Client::create([
        'name' => 'Eduardo',
        'email' => 'eduardo@betalent.tech'
    ]);

    $card = Card::create([
        'client_id' => $client->id,
        'last_four' => '1353',
        'expiration_month' => '12',
        'expiration_year' => '2032'
    ]);

    $gateway = Gateway::factory()->create([
        'name' => 'Gateway 1',
        'is_active' => 1,
        'priority' => 1
    ]);

    $transaction = Transaction::create([
        'client_id' => $client->id,
        'amount' => 1000,
        'status' => 'paid'
    ]);

    TransactionAttempt::create([
        'transaction_id' => $transaction->id,
        'gateway_id' => $gateway->id,
        'card_id' => $card->id,
        'status' => 'paid',
        'external_id' => 'ext_123'
    ]);

    Http::fake([
        'localhost:3001/transactions/ext_123/charge_back*' => Http::response([], 200)
    ]);

    $response = $this->actingAs($user)->postJson("/api/transactions/{$transaction->id}/chargeback");

    $response->assertStatus(200);

    $this->assertDatabaseHas('transactions', [
        'id' => $transaction->id,
        'status' => 'refunded'
    ]);
});

test('USER cannot refund a transaction', function () {
    $role = Role::factory()->create([
        'name' => 'USER',
        'description' => 'Normal user.'
    ]);

    $user = User::factory()->create([
        'email' => 'user@betalent.tech',
        'password' => '12345678'
    ]);

    $user->roles()->attach($role->id);

    $client = Client::create([
        'name' => 'Eduardo',
        'email' => 'eduardo@betalent.tech'
    ]);

    $card = Card::create([
        'client_id' => $client->id,
        'last_four' => '1353',
        'expiration_month' => '12',
        'expiration_year' => '2032'
    ]);

    $gateway = Gateway::factory()->create([
        'name' => 'Gateway 1',
        'is_active' => 1,
        'priority' => 1
    ]);

    $transaction = Transaction::create([
        'client_id' => $client->id,
        'amount' => 1000,
        'status' => 'paid'
    ]);

    TransactionAttempt::create([
        'transaction_id' => $transaction->id,
        'gateway_id' => $gateway->id,
        'card_id' => $card->id,
        'status' => 'paid',
        'external_id' => 'ext_123'
    ]);

    $response = $this->actingAs($user)->postJson("/api/transactions/{$transaction->id}/chargeback");

    $response->assertStatus(403);
});