<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RbacSeeder extends Seeder
{
    public function run(): void
    {
        // Limpia la caché de permisos/roles
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Permisos de ejemplo
        $permissions = [
            'users.view',
            'users.create',
            'users.edit',
            'users.delete',
            'posts.view',
            'posts.create',
            'posts.edit',
            'posts.delete',
            'settings.manage',
            'view.all.media',
        ];

        foreach ($permissions as $name) {
            Permission::firstOrCreate([
                'name'       => $name,
                'guard_name' => 'web',
            ]);
        }

        // Roles
        $admin  = Role::firstOrCreate(['name' => 'admin',  'guard_name' => 'web']);
        $editor = Role::firstOrCreate(['name' => 'editor', 'guard_name' => 'web']);
        $viewer = Role::firstOrCreate(['name' => 'viewer', 'guard_name' => 'web']);

        // Relaciones rol-permisos (esto llena role_has_permissions)
        $admin->syncPermissions(Permission::all());

        $editor->syncPermissions(Permission::whereIn('name', [
            'posts.view', 'posts.create', 'posts.edit',
        ])->get());

        $viewer->syncPermissions(Permission::whereIn('name', [
            'posts.view', 'users.view',
        ])->get());

        // Limpia caché nuevamente por seguridad
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }
}