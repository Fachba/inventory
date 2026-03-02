<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'role_id' => 1,
                'user_name' => 'admin morek',
                'user_email' => 'admin@mail.com',
                'password' => Hash::make('123456'),
                'is_active' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'role_id' => 2,
                'user_name' => 'spv morek',
                'user_email' => 'spv@mail.com',
                'password' => Hash::make('123456'),
                'is_active' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'role_id' => 3,
                'user_name' => 'gudang morek',
                'user_email' => 'gudang@mail.com',
                'password' => Hash::make('123456'),
                'is_active' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'role_id' => 4,
                'user_name' => 'teknisi morek',
                'user_email' => 'teknisi@mail.com',
                'password' => Hash::make('123456'),
                'is_active' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}