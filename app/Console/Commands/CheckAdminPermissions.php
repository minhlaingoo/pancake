<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class CheckAdminPermissions extends Command
{
    protected $signature = 'admin:check-permissions';
    protected $description = 'Check admin user permissions';

    public function handle()
    {
        $user = User::where('email', 'admin@iprogen.com')->with('role.permissions.feature')->first();
        
        if (!$user) {
            $this->error('❌ Admin user not found!');
            return 1;
        }
        
        $this->info("👤 Admin User: {$user->name} ({$user->email})");
        
        if (!$user->role) {
            $this->error('❌ No role assigned!');
            return 1;
        }
        
        $this->info("🎭 Role: {$user->role->name}");
        
        if ($user->role->permissions->isEmpty()) {
            $this->error('❌ No permissions assigned to role!');
            return 1;
        }
        
        $permissionCount = $user->role->permissions->count();
        $this->info("🔑 Permissions: {$permissionCount} total");
        
        // Group permissions by feature
        $permissionsByFeature = $user->role->permissions->groupBy(function($permission) {
            return $permission->feature ? $permission->feature->name : 'Unknown';
        });
        
        foreach ($permissionsByFeature as $feature => $permissions) {
            $permNames = $permissions->pluck('name')->join(', ');
            $this->line("  📁 {$feature}: {$permNames}");
        }
        
        // Check specifically for preset permissions
        $presetPermissions = $user->role->permissions->filter(function($permission) {
            return $permission->feature && $permission->feature->name === 'preset';
        });
        
        if ($presetPermissions->isNotEmpty()) {
            $this->info("");
            $this->info("✅ Preset permissions found:");
            foreach ($presetPermissions as $perm) {
                $this->line("  ✓ preset:{$perm->name}");
            }
        } else {
            $this->warn("⚠ No preset permissions found!");
        }
        
        return 0;
    }
}