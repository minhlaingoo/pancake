<?php

namespace App\Services;

use App\Http\Resources\ActivityLog\RolePermissionLogResource;
use App\Models\Role;
use Exception;
use Illuminate\Support\Facades\DB;

class RolePermissionService
{

    public function store($role_name, $permissions)
    {
        try {

            DB::beginTransaction();

            $role = Role::create([
                'name' => $role_name,
            ]);

            $role->permissions()->attach($permissions);

            DB::commit();

            activity('role-permission')
                ->log("Role {$role->name} has been created")
                ->event('create')
                ->properties([
                    ...(new RolePermissionLogResource($role))->resolve(),
                ])
                ->status('success')
                ->save();
        } catch (Exception $e) {
            DB::rollBack();
            activity('role-permission')
                ->log($e->getMessage())
                ->event('create')
                ->properties([
                    'name' => $role_name,
                    'permission_ids' => $permissions
                ])
                ->status('fail')
                ->save();
            dd($e->getMessage());
        }
    }
}
