<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use App\Models\Gateway;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::create(['name' => 'ADMIN']);
        Role::create(['name' => 'MANAGER']);
        Role::create(['name' => 'FINANCE']);
        Role::create(['name' => 'USER']);

        $admin = User::create([
            'email' => 'admin@betalent.tech',
            'password' => Hash::make('12345678'),
        ]);

        $admin->roles()->attach($adminRole->id);

        Gateway::create([
            'name' => 'Gateway 1',
            'is_active' => true,
            'priority' => 1
        ]);

        Gateway::create([
            'name' => 'Gateway 2',
            'is_active' => true,
            'priority' => 2
        ]);

        Product::create(['name' => 'Smartphone', 'amount' => 150000, 'stock' => 50]);
        Product::create(['name' => 'Notebook', 'amount' => 450000, 'stock' => 20]);
        Product::create(['name' => 'Mouse Gamer', 'amount' => 25000, 'stock' => 100]);
    }
}