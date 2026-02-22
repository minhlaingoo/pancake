<?php

namespace Database\Factories;

use App\Models\Feature;
use App\Models\Permission;

class FeaturePermissionFactory
{
    public function createFeatureWithPermissions($features, $permissions)
    {
        $featureFactory = new FeatureFactory();
        $permissionFactory = new PermissionFactory();

        foreach ($features as $feature) {
            $featureData = $featureFactory->createFeatureIfNotExists($feature);

            foreach ($permissions as $permission) {
                $permissionFactory->createPermissionIfNotExists($permission, $featureData->id);
            }
        }
    }
}
