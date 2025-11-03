<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Admin',
            'email' => 'admin@nanaska.com',
            'email_verified_at' => now(),
            'password' => Hash::make('nanaska123'),
            'remember_token' => Str::random(10),
        ]);
    }
}
