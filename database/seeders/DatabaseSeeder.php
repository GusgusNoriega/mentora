<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RbacSeeder::class,
            DefaultUserSeeder::class,
            PassportKeysSeeder::class,
            CleanUploadsSeeder::class,
        ]);
    }
}