<?php

use App\Models\RolePermission;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

function getPermissions()
{
    $user = Auth::user();
    return Cache::remember("user_permissions_{$user->id}", now()->addMinutes(30), function () use ($user) {
        return RolePermission::select('permissions.name as permission', 'features.name as feature')
            ->where('role_id', $user->role_id)
            ->leftJoin('permissions', 'permissions.id', 'role_permissions.permission_id')
            ->leftJoin('features', 'features.id', 'permissions.feature_id')
            ->get();
    });
}

function checkPermission(String $feature, String $permission): bool
{

    $authPermissions = getPermissions();
    foreach ($authPermissions as $p) {
        if ($p->permission == $permission && $p->feature == $feature) {
            return true;
        }
    }
    return false;
}
