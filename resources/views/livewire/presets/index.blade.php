<section class="space-y-4">
    <x-alert />
    <div class="flex justify-between items-center mb-4">
        <div>
            <mijnui:breadcrumbs>
                <mijnui:breadcrumbs.item isLast>Presets</mijnui:breadcrumbs.item>
            </mijnui:breadcrumbs>
            <h2 class="text-2xl font-semibold">Command Presets</h2>
        </div>
        <a href="{{ route('presets.create') }}" wire:navigate>
            <mijnui:button color="primary">Create Preset</mijnui:button>
        </a>
    </div>

    <div class="w-full overflow-x-auto">
        <mijnui:table :paginate="$presets">
            <mijnui:table.columns>
                <mijnui:table.column class="w-40">Preset Name</mijnui:table.column>
                <mijnui:table.column class="w-20">Version</mijnui:table.column>
                <mijnui:table.column class="w-20">Steps</mijnui:table.column>
                <mijnui:table.column class="w-32">Author</mijnui:table.column>
                <mijnui:table.column class="w-32">Date</mijnui:table.column>
                <mijnui:table.column class="w-24">Status</mijnui:table.column>
                <mijnui:table.column class="w-32">Actions</mijnui:table.column>
            </mijnui:table.columns>

            <mijnui:table.rows>
                @forelse($presets as $preset)
                    <mijnui:table.row>
                        <mijnui:table.cell>
                            <span class="font-medium">{{ $preset->name }}</span>
                        </mijnui:table.cell>
                        <mijnui:table.cell>
                            <mijnui:badge variant="outline" size="xs">{{ $preset->version }}</mijnui:badge>
                        </mijnui:table.cell>
                        <mijnui:table.cell>
                            <mijnui:badge variant="outline">
                                {{ count($preset->commands ?? []) }} Steps
                            </mijnui:badge>
                        </mijnui:table.cell>
                        <mijnui:table.cell>{{ $preset->author ?? '-' }}</mijnui:table.cell>
                        <mijnui:table.cell>{{ $preset->created_at->format('d M Y') }}</mijnui:table.cell>
                        <mijnui:table.cell>
                            @php
                                $statusColor = match ($preset->status) {
                                    'Validated' => 'success',
                                    'Draft' => 'warning',
                                    'Error' => 'danger',
                                    default => 'secondary'
                                };
                            @endphp
                            <mijnui:badge color="{{ $statusColor }}">
                                {{ $preset->status }}
                            </mijnui:badge>
                        </mijnui:table.cell>
                        <mijnui:table.cell>
                            <div class="flex gap-2">
                                <a href="{{ route('presets.edit', $preset) }}" wire:navigate>
                                    <mijnui:button size="sm" color="primary" variant="outline">
                                        Edit
                                    </mijnui:button>
                                </a>
                                <mijnui:button 
                                    size="sm" 
                                    color="danger" 
                                    variant="outline"
                                    wire:click="delete({{ $preset->id }})"
                                    wire:confirm="Are you sure you want to delete this preset?">
                                    Delete
                                </mijnui:button>
                            </div>
                        </mijnui:table.cell>
                    </mijnui:table.row>
                @empty
                    <mijnui:table.row>
                        <mijnui:table.cell colspan="7" class="text-center text-muted-foreground py-8">
                            No presets found. Create your first preset to get started.
                        </mijnui:table.cell>
                    </mijnui:table.row>
                @endforelse
            </mijnui:table.rows>
        </mijnui:table>
    </div>
</section>
