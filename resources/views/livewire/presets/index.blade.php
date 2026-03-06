<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold">Command Presets</h1>
            <p class="text-muted-foreground">Manage your command group blueprints.</p>
        </div>
        <a href="{{ route('presets.create') }}" wire:navigate>
            <mijnui:button color="primary">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                Create Preset
            </mijnui:button>
        </a>
    </div>

    @if (session()->has('message'))
        <div class="bg-success/10 text-success p-4 rounded-lg border border-success/20">
            {{ session('message') }}
        </div>
    @endif

    <mijnui:card>
        <mijnui:card.content class="p-0">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b bg-muted/50">
                        <th class="text-left p-4 font-semibold">Preset Name</th>
                        <th class="p-4 font-semibold text-center italic opacity-70">Version</th>
                        <th class="p-4 font-semibold text-center">Steps</th>
                        <th class="text-left p-4 font-semibold">Author</th>
                        <th class="text-left p-4 font-semibold">Date</th>
                        <th class="text-left p-4 font-semibold">Status</th>
                        <th class="text-right p-4 font-semibold">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($presets as $preset)
                        <tr class="hover:bg-muted/30 transition-colors">
                            <td class="p-4 font-medium">{{ $preset->name }}</td>
                            <td class="p-4 text-center">
                                <span class="bg-muted px-2 py-0.5 rounded text-[10px]">{{ $preset->version }}</span>
                            </td>
                            <td class="p-4 text-center">
                                <mijnui:badge variant="outline">
                                    {{ count($preset->commands ?? []) }} Steps
                                </mijnui:badge>
                            </td>
                            <td class="p-4 text-muted-foreground">{{ $preset->author ?? '-' }}</td>
                            <td class="p-4 text-muted-foreground text-sm">{{ $preset->created_at->format('Y-m-d') }}</td>
                            <td class="p-4">
                                @php
                                    $statusColor = match ($preset->status) {
                                        'Validated' => 'success',
                                        'Draft' => 'warning',
                                        'Error' => 'destructive',
                                        default => 'secondary'
                                    };
                                @endphp
                                <mijnui:badge color="{{ $statusColor }}">
                                    {{ $preset->status }}
                                </mijnui:badge>
                            </td>
                            <td class="p-4 text-right space-x-2">
                                <a href="{{ route('presets.edit', $preset) }}" wire:navigate>
                                    <mijnui:button variant="outline" size="sm">
                                        Edit
                                    </mijnui:button>
                                </a>
                                <mijnui:button wire:click="delete({{ $preset->id }})"
                                    wire:confirm="Are you sure you want to delete this preset?" variant="ghost" size="sm"
                                    class="text-destructive hover:bg-destructive/10">
                                    Delete
                                </mijnui:button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-8 text-center text-muted-foreground">
                                No presets found. Create your first blueprint to get started.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </mijnui:card.content>
    </mijnui:card>

    <div class="mt-4">
        {{ $presets->links() }}
    </div>
</div>