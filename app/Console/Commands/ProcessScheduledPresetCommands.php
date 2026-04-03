<?php

namespace App\Console\Commands;

use App\Jobs\RunPresetCommandJob;
use App\Models\ScheduledPresetCommand;
use Illuminate\Console\Command;

class ProcessScheduledPresetCommands extends Command
{
    protected $signature = 'preset:process-scheduled';
    protected $description = 'Dispatch pending preset commands whose execute_at has passed';

    public function handle(): void
    {
        // Single indexed query: status + execute_at
        $due = ScheduledPresetCommand::where('status', 'pending')
            ->where('execute_at', '<=', now())
            ->orderBy('execute_at')
            ->orderBy('command_index')
            ->limit(50) // prevent overloading the queue in one tick
            ->get();

        if ($due->isEmpty()) {
            return;
        }

        // Mark as dispatched first to prevent duplicate pickup on next tick
        ScheduledPresetCommand::whereIn('id', $due->pluck('id'))
            ->update(['status' => 'dispatched']);

        foreach ($due as $scheduled) {
            RunPresetCommandJob::dispatch($scheduled->id);
        }
    }
}
