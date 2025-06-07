<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Category::create([
            'name'=>'Work',
            'description'=>'my work tasks'
        ]);

        Category::create([
            'name'=>'Personal',
            'description'=>'my personal tasks'
        ]);

        Category::create([
            'name'=>'Urgent',
            'description'=>'my Urgent tasks'
        ]);
    }
}
