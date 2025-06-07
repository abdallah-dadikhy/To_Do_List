<?php

namespace Database\Seeders;

use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $owner = User::where('role', 'owner')->first();
        if (!$owner) return;

        Task::create([
            'user_id' => $owner->id,
            'title' => 'Go to the gym',
            'description' => 'Doing chest and triceps exercises',
            'is_completed' => false,
            'due_date' => now()->addDays(3),
            'priority_id' => 1,
            'category_id' => 1,
        ]);

        Task::create([
            'user_id' => $owner->id,
            'title' => 'Go to university',
            'description' => 'Presentation and discussion of the site and application',
            'is_completed' => false,
            'due_date' => now()->addWeek(),
            'priority_id' => 2,
            'category_id' => 2,
        ]);
    }
}

