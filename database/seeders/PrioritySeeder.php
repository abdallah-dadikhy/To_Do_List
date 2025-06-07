<?php

namespace Database\Seeders;

use App\Models\Priority;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PrioritySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Priority::create([
            'name'=>'High',
            'level'=>'1'
        ]);

        Priority::create([
            'name'=>'Medium',
            'level'=>'2'
        ]);
        
        Priority::create([
            'name'=>'Low',
            'level'=>'3'
        ]);
    }
}
