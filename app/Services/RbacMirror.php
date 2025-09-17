<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RbacMirror
{
    private function otherGuard(string $guard): string
    {
        return $guard === 'web' ? 'api' : 'web';
    }

    private function forgetCache(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }

    public function mirrorPermissionCreated(Permission $permission): void
    {
        $targetGuard = $this->otherGuard($permission->guard_name);
        Permission::firstOrCreate([
            'name' => $permission->name,
            'guard_name' => $targetGuard,
        ]);
        $this->forgetCache();
    }

    public function mirrorPermissionUpdated(Permission $permission, ?string $oldName = null, ?string $oldGuard = null): void
    {
        $targetGuard = $this->otherGuard($permission->guard_name);
        $lookupName = $oldName ?? $permission->name;
        $counterpart = Permission::where('name', $lookupName)->where('guard_name', $targetGuard)->first();
        if (!$counterpart) {
            $counterpart = Permission::firstOrCreate([
                'name' => $lookupName,
                'guard_name' => $targetGuard,
            ]);
        }
        if ($counterpart->name !== $permission->name) {
            $counterpart->name = $permission->name;
            $counterpart->save();
        }
        $this->forgetCache();
    }

    public function mirrorPermissionDeleted(Permission $permission): void
    {
        $targetGuard = $this->otherGuard($permission->guard_name);
        $counterpart = Permission::where('name', $permission->name)->where('guard_name', $targetGuard)->first();
        if ($counterpart) {
            $counterpart->delete();
        }
        $this->forgetCache();
    }

    public function mirrorRoleCreated(Role $role): void
    {
        $targetGuard = $this->otherGuard($role->guard_name);
        $targetRole = Role::firstOrCreate([
            'name' => $role->name,
            'guard_name' => $targetGuard,
        ]);

        // Replicar permisos actuales del rol origen (si hubiera) por nombre
        $sourcePerms = $role->permissions()->get();
        if ($sourcePerms->isNotEmpty()) {
            $targetPerms = $this->mapPermissionsToGuardByName($sourcePerms, $targetGuard, true);
            $targetRole->syncPermissions($targetPerms->all());
        }
        $this->forgetCache();
    }

    public function mirrorRoleUpdated(Role $role, ?string $oldName = null, ?string $oldGuard = null): void
    {
        $targetGuard = $this->otherGuard($role->guard_name);
        $lookupName = $oldName ?? $role->name;
        $counterpart = Role::where('name', $lookupName)->where('guard_name', $targetGuard)->first();
        if (!$counterpart) {
            $counterpart = Role::firstOrCreate([
                'name' => $lookupName,
                'guard_name' => $targetGuard,
            ]);
        }
        if ($counterpart->name !== $role->name) {
            $counterpart->name = $role->name;
            $counterpart->save();
        }
        // Las relaciones se sincronizan en attach/sync/detach
        $this->forgetCache();
    }

    public function mirrorRoleDeleted(Role $role): void
    {
        $targetGuard = $this->otherGuard($role->guard_name);
        $counterpart = Role::where('name', $role->name)->where('guard_name', $targetGuard)->first();
        if ($counterpart) {
            $counterpart->delete();
        }
        $this->forgetCache();
    }

    public function attachPermissions(Role $sourceRole, Collection $sourcePermissions): void
    {
        $targetGuard = $this->otherGuard($sourceRole->guard_name);
        $targetRole = $this->ensureRoleMirror($sourceRole, $targetGuard);
        $targetPerms = $this->mapPermissionsToGuardByName($sourcePermissions, $targetGuard, true);
        if ($targetPerms->isNotEmpty()) {
            $targetRole->givePermissionTo($targetPerms->all());
        }
        $this->forgetCache();
    }

    public function syncPermissions(Role $sourceRole, Collection $sourcePermissions): void
    {
        $targetGuard = $this->otherGuard($sourceRole->guard_name);
        $targetRole = $this->ensureRoleMirror($sourceRole, $targetGuard);
        $targetPerms = $this->mapPermissionsToGuardByName($sourcePermissions, $targetGuard, true);
        $targetRole->syncPermissions($targetPerms->all());
        $this->forgetCache();
    }

    public function detachPermissions(Role $sourceRole, Collection $sourcePermissions): void
    {
        $targetGuard = $this->otherGuard($sourceRole->guard_name);
        $targetRole = $this->ensureRoleMirror($sourceRole, $targetGuard);
        $targetPerms = $this->mapPermissionsToGuardByName($sourcePermissions, $targetGuard, false);
        if ($targetPerms->isNotEmpty()) {
            $targetRole->revokePermissionTo($targetPerms->all());
        }
        $this->forgetCache();
    }

    private function ensureRoleMirror(Role $sourceRole, string $targetGuard): Role
    {
        return Role::firstOrCreate([
            'name' => $sourceRole->name,
            'guard_name' => $targetGuard,
        ]);
    }

    private function mapPermissionsToGuardByName(Collection $sourcePermissions, string $targetGuard, bool $createMissing = false): Collection
    {
        $names = $sourcePermissions->pluck('name')->unique()->values();
        $existing = Permission::whereIn('name', $names)->where('guard_name', $targetGuard)->get()->keyBy('name');

        $result = collect();
        foreach ($names as $name) {
            $perm = $existing->get($name);
            if (!$perm && $createMissing) {
                $perm = Permission::create([
                    'name' => $name,
                    'guard_name' => $targetGuard,
                ]);
            }
            if ($perm) {
                $result->push($perm);
            }
        }

       return $result;
    }
}