<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Domain\Auth\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Brand::factory(20)->create();

        Category::factory(10)
            ->has(Product::factory(rand(5, 15)))
            ->create();

        User::query()->create([
            'name' => 'Alena',
            'email' => '89262812867@ya.ru',
            'password' => Hash::make(12345678),
        ]);

    }
}
