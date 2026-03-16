<?php

use App\Models\Transaction;
use App\Models\Client;
use App\Models\Product;
use App\Models\Card;
use App\Models\Gateway;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;

pest()->use(RefreshDatabase::class);

test('checkout', function () {
    $products = Product::factory()->createMany([
        ['name' => 'Celular', 'amount' => 12000, 'stock' => 10],
        ['name' => 'Carregador', 'amount' => 120, 'stock' => 10],
    ]);

    $payload = [
        'client_name' => 'Eduardo',
        'client_email' => 'eduardo@gmail.com',
        'card' => [
            'last_four' => '1353',
            'expiration_month' => '12',
            'expiration_year' => '2032',
            'cvv' => '010'
        ],
        'products' => [
            [
                'id' => $products[0]->id,
                'quantity' => 1
            ],
            [
                'id' => $products[1]->id,
                'quantity' => 1
            ]
        ]
    ];

    $response = $this->postJson('/api/transactions', $payload);

    $response->assertStatus(201);

    $this->assertDatabaseHas('products', ['name' => 'Celular']);

    $this->assertDatabaseHas('transactions', ['amount' => 12120, 'status' => 'pending']);

    $this->assertDatabaseHas('transaction_product', ['product_id' => $products[0]->id]);

    $this->assertDatabaseHas('transaction_product', ['product_id' => $products[1]->id]);
});

test('it should fallback to second gateway when first fails', function () {
    $gateways = Gateway::factory()->createMany([
        ['name' => 'Gateway 1', 'is_active' => 1, 'priority' => 1],
        ['name' => 'Gateway 2', 'is_active' => 1, 'priority' => 2]
    ]);

    $products = Product::factory()->createMany([
        ['name' => 'Celular', 'amount' => 12000, 'stock' => 10],
        ['name' => 'Carregador', 'amount' => 120, 'stock' => 10],
    ]);

    Http::fake([
        '*3001/transactions*' => Http::response([], 400),
        '*3002/transacoes*' => Http::response(['id_externo' => '123'], 201)
    ]);

    $payload = [
        'client_name' => 'Eduardo',
        'client_email' => 'eduardo@gmail.com',
        'card' => [
            'last_four' => '1353',
            'expiration_month' => '12',
            'expiration_year' => '2032',
            'cvv' => '100'
        ],
        'products' => [
            [
                'id' => $products[0]->id,
                'quantity' => 1
            ],
            [
                'id' => $products[1]->id,
                'quantity' => 1
            ]
        ]
    ];

    $response = $this->postJson('/api/transactions', $payload);

    $response->assertStatus(201);

    $this->assertDatabaseHas('transactions', ['amount' => 12120, 'status' => 'paid']);

    Http::assertSent(function ($request) {
        return str_contains($request->url(), '3001/transactions');
    });

    Http::assertSent(function ($request) {
        return str_contains($request->url(), '3002/transacoes');
    });
});