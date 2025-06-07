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
        User::create([
            'name'=>'Abdallah dadikhy',
            'email'=>'abdallahdadikhy@gmail.com',
            'password'=>Hash::make('123123123'),
            'role'=>'owner'
        ]);

        User::create([
            'name'=>'Mohamad albadawy',
            'email'=>'mohamadalbadawy@gmail.com',
            'password'=>Hash::make('123123123'),
            'role'=>'guest'
        ]);
    }
}
