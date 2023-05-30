<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\AdminUser;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        
        AdminUser::insert([
            [ 
                'role_id' => 1,
                'first_name' => 'John',
                'last_name' => 'Dave',
                'mobile' => '9929104237',
                'email' => 'admin@admin.com',
                'password' => bcrypt('123456'),
                'email_verified_at' => date('Y-m-d H:i:s'),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
                ]
        ]);  
    }
}
