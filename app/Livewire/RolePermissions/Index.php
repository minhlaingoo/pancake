<?php

namespace App\Livewire\RolePermissions;

use App\Models\Role;
use Exception;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Index extends Component
{

    public $perPage = 10;
    public function delete($id)
    {
        if (!checkPermission('role', 'delete')) return abort(403);

        try {
            DB::beginTransaction();
            $role = Role::findOrFail($id);
            if ($role->users) {
                session()->flash('error', 'Users with this role must be deleted or changed role first!');
                return redirect()->back();
            }
            $role->permissions()->detach();
            $role->delete();
            DB::commit();

            activity('role-permission')
                ->log("role {$role->name} has been deleted")
                ->event('delete')
                ->properties([
                    'id' => $id,
                ])
                ->status('success')
                ->save();
        } catch (Exception $e) {
            DB::rollBack();
            activity('role-permission')
                ->log($e->getMessage())
                ->event('delete')
                ->properties([
                    'id' => $id,
                ])
                ->status('fail')
                ->save();
            session()->flash('error', 'Role delete failed!');
            return redirect()->back();
        }
    }

    public function render()
    {
        return view(
            'livewire.role-permissions.index',
            [
                'roles' => Role::paginate(10)
            ]
        );
    }
}
