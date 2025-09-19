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

        // Usuario adicional 1
        $user2 = User::updateOrCreate(
            ['email' => 'atencion@britishhouseinternational.net'],
            [
                'name'              => 'Admin Atencion',
                'password'          => Hash::make('12345678'),
                'email_verified_at' => now(),
            ]
        );

        if (! $user2->hasRole('admin')) {
            $user2->assignRole('admin');
        }

        // Usuario adicional 2
        $user3 = User::updateOrCreate(
            ['email' => 'izamar_lucely@hotmail.com'],
            [
                'name'              => 'Izamar Lucely',
                'password'          => Hash::make('12345678'),
                'email_verified_at' => now(),
            ]
        );

        if (! $user3->hasRole('admin')) {
            $user3->assignRole('admin');
        }
    }
}