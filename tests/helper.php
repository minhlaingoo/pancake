<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

function createSampleUser($email = 'test@example.com', $password = 'password', $role_id = 1)
{
    return User::factory()->create([
        'email' => $email,
        'password' => Hash::make($password),
        'role_id' => $role_id
    ]);
}
