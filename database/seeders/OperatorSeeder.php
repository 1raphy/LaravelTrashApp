<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class OperatorSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'name' => 'Operator',
            'email' => 'operator@gmail.com',
            'phone' => '081234567890',
            'password' => Hash::make('operator123'),
            'role' => 'operator',
        ]);
    }
}
