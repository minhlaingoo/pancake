<?php

namespace Database\Factories;

use App\Models\Permission;

class PermissionFactory
{

    public function createPermissionIfNotExists($name, $featureId)
    {
        $existingRole = Permission::where('name', $name)
            ->where('feature_id', $featureId)
            ->first();

        if (!$existingRole) {

            return Permission::create([
            'name' => $name,
            'feature_id' => $featureId,
            'created_at' => now(),
            'updated_at' => now(),
            ]);

        }

        return $existingRole;
    }
}
