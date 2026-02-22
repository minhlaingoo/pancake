<?php

namespace App\Livewire\Users;

use App\Http\Resources\ActivityLog\UserLogResource;
use App\Models\Role;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Edit extends Component
{
    public $name;
    public $roles = [];
    public $role_id;
    public $email;
    public $password, $password_confirmation;
    public $is_active;
    public $user;


    public function mount($id)
    {
        $this->user = User::withoutAdmin()->where('id', $id)->first();
        if (!$this->user) abort(404);
        $this->roles = Role::get();
        $this->fill($this->user);
        $this->is_active = $this->is_active == 1;;
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => "required|email|max:255|unique:users,email,{$this->user->id}",
            'password' => 'nullable|min:8|confirmed',
            'role_id' => 'required|exists:roles,id',
        ];
    }


    public function update()
    {
        $validated_data = $this->validate();
        try {
            DB::beginTransaction();
            $this->user->fill(
                [
                    'name' => $this->name,
                    'role_id' => $this->role_id,
                    'email' => $this->email,
                    'is_active' => $this->is_active ?? 1,
                ]
            );

            if ($this->password) {
                $this->user->password = bcrypt($this->password);
            }

            $this->user->save();

            DB::commit();

            activity('user')
                ->log("User {$this->name} has been updated")
                ->event('update')
                ->properties([
                    ...(new UserLogResource($this->user))->resolve(),
                ])
                ->status('success')
                ->save();

            session()->flash('message', 'User updated successfully.');
            return redirect()->route('users.index');
        } catch (Exception $e) {
            DB::rollBack();
            activity('user')
                ->log($e->getMessage())
                ->event('update')
                ->properties([
                    'id' => $this->user->id,
                    ...$validated_data
                ])
                ->status('fail')
                ->save();
            session()->flash('error', 'User update failed.');
            return redirect()->route('users.index');
        }
    }

    public function render()
    {
        return view('livewire.users.edit');
    }
}
