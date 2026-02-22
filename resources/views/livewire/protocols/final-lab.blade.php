<div class="flex gap-4">
    <mijnui:card class="max-w-2xl">
        <mijnui:card.header>
            <mijnui:card.title class="text-lg font-semibold">
                Reaction Overview
            </mijnui:card.title>
        </mijnui:card.header>

        <mijnui:card.content>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <mijnui:input label="Sample ID" wire:model="sample_id" disabled readonly />
                <mijnui:input label="Comment" wire:model="description" disabled readonly />
            </div>

            <div class="mt-6 space-y-4">
                <div class="grid grid-cols-6 gap-4 items-center">
                    <mijnui:label>mAb Volume</mijnui:label>
                    <div class="col-span-4">
                        <mijnui:input wire:model="formData.mAb.volume" placeholder="Volume" />
                    </div>
                    <mijnui:input value="mL" disabled />
                </div>

                <div class="grid grid-cols-6 gap-4 items-center">
                    <mijnui:label>Payload Volume</mijnui:label>
                    <div class="col-span-4">
                        <mijnui:input wire:model="formData.payload.volume_to_add" placeholder="Volume" />
                    </div>
                    <mijnui:input value="mL" disabled />
                </div>

                <div class="grid grid-cols-6 gap-4 items-center">
                    <mijnui:label>Reduction Reservoir</mijnui:label>
                    <div class="col-span-4">
                        <mijnui:input wire:model="formData.misc.reduction_reservoir" placeholder="Volume" />
                    </div>
                    <mijnui:input value="mL" disabled />
                </div>

                <div class="grid grid-cols-6 gap-4 items-center">
                    <mijnui:label>Additive Reservoir A</mijnui:label>
                    <div class="col-span-4">
                        <mijnui:input wire:model="formData.misc.additive_reservoir_a" placeholder="Volume" />
                    </div>
                    <mijnui:input value="mL" disabled />
                </div>

                <div class="grid grid-cols-6 gap-4 items-center">
                    <mijnui:label>Additive Reservoir B</mijnui:label>
                    <div class="col-span-4">
                        <mijnui:input wire:model="formData.misc.additive_reservoir_b" placeholder="Volume" />
                    </div>
                    <mijnui:input value="mL" disabled />
                </div>

                <div class="grid grid-cols-6 gap-4 items-center">
                    <mijnui:label>Additive Reservoir C</mijnui:label>
                    <div class="col-span-4">
                        <mijnui:input wire:model="formData.misc.additive_reservoir_c" placeholder="Volume" />
                    </div>
                    <mijnui:input value="mL" disabled />
                </div>

                <h3 class="text-lg font-semibold pt-2">Desired Final Product</h3>

                <div class="grid grid-cols-6 gap-4 items-center">
                    <mijnui:label>Desired Final Volume</mijnui:label>
                    <div class="col-span-4">
                        <mijnui:input wire:model="formData.misc.desired_final_volume" placeholder="Volume" />
                    </div>
                    <mijnui:input value="mL" disabled />
                </div>
            </div>

            <div class="flex items-center gap-4 my-6">
                <mijnui:checkbox wire:model="formData.misc.buffer_level_checked" />
                <span>Buffer Level Checked?</span>
            </div>

            <div class="flex items-center gap-4">
                <mijnui:button color="primary" wire:click="save">Confirm</mijnui:button>
                <mijnui:button link="{{ route('protocols.index') }}">Back</mijnui:button>
            </div>
        </mijnui:card.content>
    </mijnui:card>

    <mijnui:card>
        <mijnui:card.header>
            <mijnui:card.title class="text-lg font-semibold">
                Phases
            </mijnui:card.title>
        </mijnui:card.header>
        <mijnui:card.content>
            <div class="space-y-4">
                <mijnui:input wire:model="phaseFormData.label" label="Label" required />

                <div class="grid grid-cols-2 gap-4">
                    <mijnui:input type="number" wire:model="phaseFormData.duration" label="Duration" required />
                    <mijnui:input type="number" wire:model="phaseFormData.loop" label="Loop" required />
                </div>

                <mijnui:button wire:click="addPhase">Create</mijnui:button>
            </div>

            <div>
                <h3>Phases Container</h3>
                <div>
                    <div class="space-y-4">
                        {{-- 1. Sequential Processing Phases --}}
                        @foreach (collect($phases)->filter(fn($p) => !($p['is_end'] ?? false)) as $index => $phase)
                            <div class="p-4 border rounded-lg shadow bg-background">
                                <div class="flex justify-between items-center mb-4">
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="h-6 w-6 rounded-lg bg-primary/10 flex items-center justify-center shrink-0">
                                                <span class="text-[10px] font-bold text-primary">{{ $index + 1 }}</span>
                                            </div>
                                            <mijnui:input wire:model="phases.{{ $index }}.label"
                                                class="font-semibold text-lg border-none bg-transparent focus:ring-0 p-0 w-full"
                                                placeholder="Phase Name" />
                                        </div>
                                    </div>
                                    <button wire:click="removePhase('{{ $phase['id'] }}')"
                                        class="relative z-10 text-muted-foreground hover:text-destructive transition-colors p-1 rounded-md hover:bg-destructive/10"
                                        title="Remove Phase">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                            stroke-linejoin="round">
                                            <path d="M3 6h18" />
                                            <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6" />
                                            <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2" />
                                            <line x1="10" y1="11" x2="10" y2="17" />
                                            <line x1="14" y1="11" x2="14" y2="17" />
                                        </svg>
                                    </button>
                                </div>

                                {{-- Common Phase Body (Duration/Loop/Commands) --}}
                                @include('livewire.protocols.partials.phase-body', ['index' => $index, 'phase' => $phase])
                            </div>
                        @endforeach

                        {{-- 2. Finalization Section --}}
                        @php
                            $endIdx = collect($phases)->search(fn($p) => ($p['is_end'] ?? false) === true);
                        @endphp

                        @if($endIdx !== false)
                            <div class="mt-12 pt-8 border-t-2 border-dashed">
                                <div class="flex items-center gap-2 mb-4">
                                    <div class="h-8 w-8 rounded-lg bg-warning/10 flex items-center justify-center">
                                        <i class="fas fa-flag-checkered text-warning text-sm"></i>
                                    </div>
                                    <h3 class="text-base font-bold text-foreground">Finalization / Cleanup Actions</h3>
                                </div>

                                <div class="p-4 border-2 border-warning/20 rounded-xl shadow-sm bg-warning/5">
                                    <div class="mb-4">
                                        <mijnui:input wire:model="phases.{{ $endIdx }}.label"
                                            class="font-bold text-lg border-none bg-transparent focus:ring-0 p-0 w-full text-warning"
                                            placeholder="End Phase Name" />
                                        <p class="text-[10px] text-warning/70 uppercase font-semibold tracking-wider">
                                            Executed automatically upon protocol completion
                                        </p>
                                    </div>

                                    @include('livewire.protocols.partials.phase-body', ['index' => $endIdx, 'phase' => $phases[$endIdx]])
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

            </div>
        </mijnui:card.content>
    </mijnui:card>
</div>