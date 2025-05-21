<?php

namespace Database\Seeders;

use App\Models\Project;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 1; $i <= 10; $i++) {
            Project::create([
                'client_id' => $i,
                'title' => "Project for Client {$i}",
                'description' => "Redesign project tailored for Client {$i}.",
                'status' => ['active', 'completed'][rand(0, 1)],
                'deadline' => now()->addDays(rand(10, 90))->toDateString(),
            ]);
        }

    }
}
