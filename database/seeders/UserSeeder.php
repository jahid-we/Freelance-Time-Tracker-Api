<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Jahid Hasan',
            'email' => 'jahidhasan370919@gmail.com',
            'password' => Hash::make('123456'),
            'email_verified_at' => now(),
        ]);
    }
}
