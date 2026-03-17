<?php

namespace Database\Seeders;

use App\Models\Feature;
use App\Models\Permission;
use App\Models\RolePermission;
use Database\Factories\FeatureFactory;
use Database\Factories\FeaturePermissionFactory;
use Database\Factories\PermissionFactory;
use Database\Factories\RoleFactory;
use Database\Factories\RolePermissionFactory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DefaultRolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $roleFactory = new RoleFactory();
        $rolePermissionFactory = new RolePermissionFactory();
        $featurePermissionFactory = new FeaturePermissionFactory();

        //Features
        $basic_feature = ['user', 'role', 'device', 'protocol', 'preset', 'system-log', 'process-log', 'process'];
        $log_feature = ['activity-log', 'component-log'];
        $device_feature = ['device-config'];
        $protocol_feature = ['protocol-config'];
        $process_feature = [];


        //Permissions
        /**
         * 0 - 'view'
         * 1 - 'create'
         * 2 - 'update'
         * 3 - 'delete'
         * 4 - 'import'
         * 5 - 'export'
         * 6 - 'print'
         */

        $basic_permissions = ['view', 'create', 'update', 'delete', 'import', 'export', 'print'];
        $systemPermissions = ['install', 'uninstall', 'upload'];
        $optionalPermissions = ['real-time', 'version-approval'];
        $log_permissions = [$basic_permissions[0], $basic_permissions[5]];
        $device_pemissions = [$basic_permissions[0], $basic_permissions[1], $basic_permissions[2], $basic_permissions[3], $basic_permissions[5], $optionalPermissions[0]];
        $protocol_permissions = [$basic_permissions[0], $basic_permissions[1], $basic_permissions[2], $basic_permissions[3], ...$optionalPermissions];

        $rolesToCreate = ['Administrator'];

        //Create Features and Permissions
        $featurePermissionFactory->createFeatureWithPermissions($basic_feature, $basic_permissions);
        $featurePermissionFactory->createFeatureWithPermissions($log_feature, $log_permissions);
        //        $featurePermissionFactory->createFeatureWithPermissions($system, $vds_permissions);


        foreach ($rolesToCreate as $roleName) {
            $role = $roleFactory->createRoleIfNotExists($roleName);

            if ($roleName === 'Administrator') {
                $rolePermissionFactory->attachPermissions($role);
            }
        }
    }
}
