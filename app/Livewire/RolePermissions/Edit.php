<?php

namespace App\Livewire\RolePermissions;

use App\Http\Resources\ActivityLog\RolePermissionLogResource;
use App\Models\Feature;
use App\Models\Permission;
use App\Models\Role;
use Exception;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Edit extends Component
{

    public $id;
    public $role;
    public $selected_permissions = [];

    public function rules()
    {
        return [
            'role' => 'required|string|unique:roles,name,' . $this->id
        ];
    }

    public function updatedSelectedPermissions()
    {
        $this->selected_permissions = array_map('strval', $this->selected_permissions);
    }

    public function selectFeaturePermissions($id)
    {
        $feature_permissions = Permission::where('feature_id', $id)
            ->pluck('id')
            ->toArray();

        if (array_diff(array_map('strval', $feature_permissions), $this->selected_permissions)) {
            $this->selected_permissions = array_unique(array_merge($this->selected_permissions, array_map('strval', $feature_permissions)));
        } else {
            $this->selected_permissions = array_diff($this->selected_permissions, array_map('strval', $feature_permissions));
        }
    }

    public function update()
    {
        $this->validate();
        try {
            DB::beginTransaction();

            $role = Role::findOrFail($this->id);

            $role->fill([
                'name' => $this->role,
            ])->save();

            $role->permissions()->sync($this->selected_permissions);

            DB::commit();

            activity('role-permission')
                ->log("Role {$role->name} has been updated")
                ->event('updated')
                ->properties([
                    ...(new RolePermissionLogResource($role))->resolve(),
                ])
                ->status('success')
                ->save();

            session()->flash('message', 'Role updated successfully.');
            return to_route('role-permissions.index');
        } catch (Exception $e) {
            DB::rollBack();
            activity('role-permission')
                ->log($e->getMessage())
                ->event('update')
                ->properties([
                    'name' => $this->role,
                    'permission_ids' => $this->selected_permissions
                ])
                ->status('fail')
                ->save();
            session()->flash('error', 'Role update failed.');
            return redirect()->route('role-permissions.index');
        }
    }

    public function mount(Role $role)
    {
        $this->id = $role->id;
        $this->role = $role->name;
        $this->selected_permissions = $role->permissions()->pluck('permissions.id')->toArray();
    }

    public function render()
    {
        return view('livewire.role-permissions.edit', [
            'fPermissions' => Feature::with('permissions')->get()
        ]);
    }
}
