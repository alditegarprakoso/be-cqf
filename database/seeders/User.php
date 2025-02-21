<?php

namespace Database\Seeders;

use App\Models\User as ModelsUser;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class User extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ModelsUser::create([
            'name' => 'Aldi Tegar Prakoso',
            'email' => 'aldi@cqf.com',
            'password' => Hash::make('password'),
            'position' => 'Fullstack Developer',
            'status' => 'Aktif'
        ]);
    }
}
