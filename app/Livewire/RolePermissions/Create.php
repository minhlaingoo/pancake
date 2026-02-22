<?php

namespace App\Livewire\RolePermissions;

use App\Http\Resources\ActivityLog\RolePermissionLogResource;
use App\Models\Feature;
use App\Models\Permission;
use App\Models\Role;
use App\Models\RolePermission;
use App\Services\RolePermissionService;
use Exception;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Create extends Component
{

    public $role;
    public $selected_permissions = [];

    public function rules()
    {
        return [
            'role' => 'required|unique:roles,name',
            'selected_permissions' => 'required|array|min:1',
        ];
    }

    public function messages()
    {
        return [
            'selected_permissions.required' => "At least one permission is required"
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

    public function store(RolePermissionService $rolePermissionService)
    {
        // $rolePermissionService = new ();
        $this->validate();
        try {
            $rolePermissionService->store($this->role, $this->selected_permissions);
            session()->flash('message', 'Role created successfully.');
            return redirect()->route('role-permissions.index');
        } catch (Exception $e) {
            dd($e->getMessage());
            session()->flash('error', 'Role create failed.');
        }
    }

    public function render()
    {
        return view('livewire.role-permissions.create', [
            'fPermissions' => Feature::with('permissions')->get()
        ]);
    }
}
