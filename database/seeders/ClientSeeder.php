<?php

namespace Database\Seeders;

use App\Models\Client;
use Illuminate\Database\Seeder;

class ClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 1; $i <= 2; $i++) {
            Client::create([
                'user_id' => 1, // Make sure a user with this ID exists
                'name' => "Client {$i}",
                'email' => "contact{$i}@acme.com",
                'contact_person' => "Contact Person {$i}",
            ]);
        }

    }
}
