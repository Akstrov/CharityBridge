<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);
        // Test donor
        User::factory()->create([
            'name' => 'Test Donor',
            'email' => 'donor@example.com',
            'password' => Hash::make('password'),
            'role' => 'donor',
        ]);
        // Test charity
        User::factory()->create([
            'name' => 'Test Charity',
            'email' => 'charity@example.com',
            'password' => Hash::make('password'),
            'role' => 'charity',
        ]);

        // Regular donors
        User::factory()->count(10)->donor()->create();

        // Regular charities
        User::factory()->count(5)->charity()->create();
    }
}
