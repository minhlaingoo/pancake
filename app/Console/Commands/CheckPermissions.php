<?php

namespace App\Console\Commands;

use App\Models\Feature;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Console\Command;

class CheckPermissions extends Command
{
    protected $signature = 'permission:check';
    protected $description = 'Check current features, permissions and roles';

    public function handle()
    {
        $this->info("📊 Current Permissions System:");
        $this->info("");
        
        // Check Features
        $features = Feature::all(['name']);
        $this->info("🎯 Features ({$features->count()}):");
        foreach ($features as $feature) {
            $this->line("  • {$feature->name}");
        }
        $this->info("");
        
        // Check Permissions
        $permissions = Permission::all(['name']);
        $this->info("🔑 Permissions ({$permissions->count()}):");
        foreach ($permissions as $permission) {
            $this->line("  • {$permission->name}");
        }
        $this->info("");
        
        // Check Roles
        $roles = Role::all(['name']);
        $this->info("👤 Roles ({$roles->count()}):");
        foreach ($roles as $role) {
            $this->line("  • {$role->name}");
        }
        
        return 0;
    }
}