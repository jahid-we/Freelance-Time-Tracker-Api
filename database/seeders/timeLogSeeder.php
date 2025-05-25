<?php

namespace Database\Seeders;

use App\Models\TimeLog;
use Illuminate\Database\Seeder;

class timeLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 1; $i <= 5; $i++) {
            TimeLog::create([
                'user_id' => 1,
                'project_id' => $i,
                'start_time' => now()->subHours(rand(1, 10)),
                'end_time' => now()->subHours(rand(1, 10)),
                'description' => "Time log entry {$i}",
                'hours' => rand(1, 8),
                'tags' => ['billable', 'non-billable'][rand(0, 1)],
            ]);
        }
    }
}
