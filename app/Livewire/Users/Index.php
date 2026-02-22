<?php

namespace App\Livewire\Users;

use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Index extends Component
{
    public function delete($id)
    {
        if (!checkPermission('user', 'delete')) return abort(403);
        try {
            DB::beginTransaction();
            $user = User::findOrFail($id);
            $user->delete();
            activity('user')
                ->log("User has been deleted")
                ->event('delete')
                ->properties([
                    'id' => $id,
                ])
                ->status('success')
                ->save();
            DB::commit();
            session()->flash('message', 'User deleted successfully.');
        } catch (Exception $e) {
            DB::rollBack();
            activity('user')
                ->log($e->getMessage())
                ->event('delete')
                ->properties([
                    'id' => $id,
                ])
                ->status('fail')
                ->save();
            session()->flash('error', 'User delete failed');
            return redirect()->back();
        }
    }
    public function render()
    {

        $users = User::orderBy('id', 'desc')->paginate(8);
        return view(
            'livewire.users.index',
            [
                'users' => $users
            ]
        );
    }
}
