<?php

namespace Database\Seeders;

use App\Models\Feature;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;


class roleModuleSeeder extends Seeder
{
    private $basic_permissions = ['view', 'create', 'update', 'delete', 'import', 'export', 'print'];

    private function createFeatureAndPermissions($features, $permissions)
    {

        foreach ($features as $feature) {
            $featureData = Feature::create([
                'name' => $feature,
            ]);

            foreach ($permissions as $permission) {
                Permission::create([
                    'name' => $permission,
                    'feature_id' => $featureData->id,
                ]);
            }
        }
    }
    public function run()
    {
        $systemPermissions = ['install', 'uninstall', 'upload'];
        $vds_permissions = [$this->basic_permissions[0], $this->basic_permissions[3], ...$systemPermissions];
        $billingPermissions =  [...$this->basic_permissions, 'transfer'];

        $defaultRole = Role::where('name' , 'Administrator')->where('id',1)->first();



        $billingFeatures1 = ['Cash & Payment'];
        $billingFeatures2 = ['Expense'];
        $system = ['Module'];

        $this->createFeatureAndPermissions($billingFeatures1, $billingPermissions);
        $this->createFeatureAndPermissions($billingFeatures2, $this->basic_permissions);
        $this->createFeatureAndPermissions($system, $vds_permissions);
        // Associate permissions with the default role

        $permissions = Permission::pluck('id');
        $defaultRole->permissions()->sync($permissions);



    }
}
