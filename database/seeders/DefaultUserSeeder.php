<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DefaultUserSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::updateOrCreate(
            ['email' => 'gusgusnoriega@gmail.com'],
            [
                'name'              => 'Gustavo Noriega',
                'password'          => Hash::make('12345678'),
                'email_verified_at' => now(),
            ]
        );

        // Asignar rol admin (debe existir por el RbacSeeder)
        if (! $user->hasRole('admin')) {
            $user->assignRole('admin');
        }
    }
}