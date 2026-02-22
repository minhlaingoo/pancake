<?php

namespace App\Livewire\Users;

use App\Http\Resources\ActivityLog\UserLogResource;
use App\Models\Role;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Create extends Component
{

    public $name;
    public $roles = [];
    public $role_id;
    public $email;
    public $password, $password_confirmation;
    public $is_active = true;

    public function mount()
    {
        $this->roles = Role::all();
    }

    public function rules()
    {
        return    [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|min:8|confirmed',
            'role_id' => 'required|exists:roles,id',
        ];
    }


    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function store()
    {
        $validated_data = $this->validate();
        try {
            DB::beginTransaction();

            $user = User::create([
                'name' => $this->name,
                'role_id' => $this->role_id,
                'email' => $this->email,
                'password' => bcrypt($this->password),
                'is_active' => $this->is_active,
            ]);

            activity('user')
                ->log("User {$this->name} has been created")
                ->event('create')
                ->properties([
                    ...(new UserLogResource($user))->resolve(),
                ])
                ->status('success')
                ->save();

            DB::commit();
            session()->flash('message', 'User created successfully.');
            return redirect()->route('users.index');
        } catch (Exception $e) {
            DB::rollBack();
            activity('user')
                ->log($e->getMessage())
                ->event('create')
                ->properties([
                    ...$validated_data
                ])
                ->status('fail')
                ->save();
            session()->flash('error', 'User create failed.');
            return redirect()->route('users.index');
        }
    }

    public function render()
    {
        return view('livewire.users.create');
    }
}
