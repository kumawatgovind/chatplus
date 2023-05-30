<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\DataSync;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::insert([
            [ 
                'parent_id' => '0',
                'username' => 'john',
                'name' => 'John Dave',
                'country_code' => '+91',
                'phone_number' => '1234567890',
                'email' => 'john@gmail.com',
                'password' => bcrypt('123456'),
                'status' => 1,
                'email_verified_at' => date('Y-m-d H:i:s'),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [ 
                'parent_id' => '1',
                'username' => 'dave',
                'name' => 'John Dave',
                'country_code' => '+91',
                'phone_number' => '0987654321',
                'email' => 'dave@gmail.com',
                'password' => bcrypt('123456'),
                'status' => 1,
                'email_verified_at' => date('Y-m-d H:i:s'),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ]);
    }
}
