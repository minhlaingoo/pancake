<?php

namespace Database\Factories;

//use App\Models\Role;
use App\Models\Permission;

class RolePermissionFactory
{
    public function attachPermissions($role, $features = null)
    {

        if ($role->name == 'Administrator' || $features === null) {
            $permissions = Permission::pluck('id');
        }else {
            $permissions = Permission::whereIn('feature_id', function ($query) use ($features) {
                $query->select('id')
                    ->from('features')
                    ->whereIn('name', $features);
            })->pluck('id');
        }

        $role->permissions()->sync($permissions);

    }
}
