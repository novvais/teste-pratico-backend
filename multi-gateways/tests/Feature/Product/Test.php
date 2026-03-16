<?php

use App\Models\User;
use App\Models\Role;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;

pest()->use(RefreshDatabase::class);

test('can list all products', function () {
    $role = Role::factory()->create(['name' => 'ADMIN']);
    
    $user = User::factory()->create(['password' => '12345678']);
    
    $user->roles()->attach($role->id);

    Product::factory()->count(2)->create();

    $response = $this->actingAs($user)->getJson('/api/products');

    $response->assertStatus(200);
    
    $response->assertJsonCount(2);
});

test('can show a specific product', function () {
    $role = Role::factory()->create(['name' => 'ADMIN']);
    
    $user = User::factory()->create(['password' => '12345678']);
    
    $user->roles()->attach($role->id);

    $product = Product::factory()->create(['name' => 'Notebook', 'amount' => 5000, 'stock' => 10]);

    $response = $this->actingAs($user)->getJson("/api/products/{$product->id}");

    $response->assertStatus(200);
    
    $response->assertJsonFragment(['name' => 'Notebook']);
});

test('can update a product', function () {
    $role = Role::factory()->create(['name' => 'MANAGER']);
    
    $user = User::factory()->create(['password' => '12345678']);
    
    $user->roles()->attach($role->id);

    $product = Product::factory()->create(['name' => 'Mouse', 'amount' => 100, 'stock' => 50]);

    $response = $this->actingAs($user)->patchJson("/api/products/{$product->id}", [
        'amount' => 150
    ]);

    $response->assertStatus(200);
    
    $this->assertDatabaseHas('products', ['id' => $product->id, 'amount' => 150]);
});

test('can delete a product', function () {
    $role = Role::factory()->create(['name' => 'FINANCE']);
    
    $user = User::factory()->create(['password' => '12345678']);
    
    $user->roles()->attach($role->id);

    $product = Product::factory()->create(['name' => 'Teclado', 'amount' => 200, 'stock' => 30]);

    $response = $this->actingAs($user)->deleteJson("/api/products/{$product->id}");

    $response->assertStatus(204);
    
    $this->assertSoftDeleted('products', ['id' => $product->id]);
});