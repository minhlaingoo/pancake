<?php

namespace App\Console\Commands;

use App\Models\Preset;
use Illuminate\Console\Command;

class CheckPresets extends Command
{
    protected $signature = 'preset:check';
    protected $description = 'Check current presets in database';

    public function handle()
    {
        $presets = Preset::all(['id', 'name', 'version', 'commands']);
        
        $this->info("📊 Current Presets in Database:");
        $this->info("Total count: " . $presets->count());
        $this->info("");
        
        foreach ($presets as $preset) {
            $commandCount = is_array($preset->commands) ? count($preset->commands) : count(json_decode($preset->commands, true) ?? []);
            $this->line("• {$preset->name} (v{$preset->version}) - {$commandCount} commands");
        }
        
        return 0;
    }
}