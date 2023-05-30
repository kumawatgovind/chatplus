<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            \Database\Seeders\AdminRoleSeeder::class,
            \Database\Seeders\AdminUserSeeder::class,
            \Database\Seeders\ModuleSeeder::class,
            \Database\Seeders\UserSeeder::class,
        ]);
    }
}
