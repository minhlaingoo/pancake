<form wire:submit="finalizeProtocol" class="space-y-2">
    <x-alert />
    <mijnui:card>
        <mijnui:card.header>
            <mijnui:card.title class="text-lg font-semibold">
                Creating Protocol
            </mijnui:card.title>
        </mijnui:card.header>
        <mijnui:card.content>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <mijnui:input label="Sample ID" wire:model="sample_id" placeholder="e.g. 123" />
                <div class="w-full space-y-1">
                    <p class="text-sm">Description</p>
                    <mijnui:textarea
                        class="border-input disabled:opacity-disabled flex min-h-[80px] w-full rounded-md border bg-transparent px-3 py-2 text-sm placeholder:text-muted-text focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring disabled:cursor-not-allowed"
                        placeholder="Description about protocol" wire:model="description"></mijnui:textarea>
                </div>
            </div>
        </mijnui:card.content>
    </mijnui:card>

    <!-- mAb Card -->
    <mijnui:card>
        <mijnui:card.header>
            <mijnui:card.title class="text-lg font-semibold">mAb</mijnui:card.title>
        </mijnui:card.header>
        <mijnui:card.content class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="w-full">
                <mijnui:label>Volume</mijnui:label>
                <div class="flex items-center gap-4">
                    <div class="flex-1">
                        <mijnui:input wire:model="formData.mAb.volume" />
                    </div>
                    <mijnui:select wire:model="formData.mAb.volume_unit">
                        <mijnui:select.option value="µL">µL</mijnui:select.option>
                        <mijnui:select.option value="mL">mL</mijnui:select.option>
                    </mijnui:select>

                </div>
            </div>

            <div class="w-full">
                <mijnui:label>Concentration</mijnui:label>
                <div class="flex items-center gap-4">
                    <div class="flex-1">
                        <mijnui:input wire:model="formData.mAb.concentration" />
                    </div>
                    <mijnui:select wire:model="formData.mAb.concentration_unit">
                        <mijnui:select.option value="mg/mL">mg/mL</mijnui:select.option>
                        <mijnui:select.option value="µg/mL">µg/mL</mijnui:select.option>
                        <mijnui:select.option value="nM">nM</mijnui:select.option>
                    </mijnui:select>

                </div>
            </div>

            <mijnui:input label="Molecular Weight" wire:model="formData.mAb.molecular_weight" />
            <mijnui:input label="Molar Absorbing Coefficient" wire:model="formData.mAb.molar_absorbing_coefficient" />

            <div class="w-full">
                <mijnui:label>Volume to Add</mijnui:label>
                <div class="flex items-center gap-4">
                    <div class="flex-1">
                        <mijnui:input type="number" wire:model="formData.mAb.volume_to_add" />
                    </div>
                    <mijnui:select wire:model="formData.mAb.volume_to_add_unit">
                        <mijnui:select.option value="µL">µL</mijnui:select.option>
                        <mijnui:select.option value="mL">mL</mijnui:select.option>
                    </mijnui:select>
                </div>
            </div>
        </mijnui:card.content>
    </mijnui:card>

    <!-- Payload Card -->
    <mijnui:card>
        <mijnui:card.header>
            <mijnui:card.title class="text-lg font-semibold">Payload</mijnui:card.title>
        </mijnui:card.header>
        <mijnui:card.content class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="w-full">
                <mijnui:label>Volume Available</mijnui:label>
                <div class="flex items-center gap-4">
                    <div class="flex-1">
                        <mijnui:input wire:model="formData.payload.volume_available" />
                    </div>
                    <mijnui:select wire:model="formData.payload.volume_available_unit">
                        <mijnui:select.option value="µL">µL</mijnui:select.option>
                        <mijnui:select.option value="mL">mL</mijnui:select.option>
                    </mijnui:select>
                </div>
            </div>

            <div class="w-full">
                <mijnui:label>Concentration</mijnui:label>
                <div class="flex items-center gap-4">
                    <div class="flex-1">
                        <mijnui:input wire:model="formData.payload.concentration" />
                    </div>
                    <mijnui:select wire:model="formData.payload.concentration_unit">
                        <mijnui:select.option value="mg/mL">mg/mL</mijnui:select.option>
                        <mijnui:select.option value="µg/mL">µg/mL</mijnui:select.option>
                        <mijnui:select.option value="nM">nM</mijnui:select.option>
                    </mijnui:select>
                </div>
            </div>

            <mijnui:input label="Molecular Weight" wire:model="formData.payload.molecular_weight" />
            <mijnui:input label="Molar Equivalence" wire:model="formData.payload.molar_equivalence" />

            <div class="w-full">
                <mijnui:label>Volume to Add</mijnui:label>
                <div class="flex items-center gap-4">
                    <div class="flex-1">
                        <mijnui:input wire:model="formData.payload.volume_to_add" />
                    </div>
                    <mijnui:select wire:model="formData.payload.volume_to_add_unit">
                        <mijnui:select.option value="µL">µL</mijnui:select.option>
                        <mijnui:select.option value="mL">mL</mijnui:select.option>
                    </mijnui:select>
                </div>
            </div>

            <mijnui:input label="Molar Absorbing Coefficient"
                wire:model="formData.payload.molar_absorbing_coefficient" />
        </mijnui:card.content>
    </mijnui:card>

    <!-- Miscellaneous Card -->
    <mijnui:card x-data="{ open: true }">
        <mijnui:card.header>
            <mijnui:card.title class="text-lg font-semibold">Miscellaneous</mijnui:card.title>
        </mijnui:card.header>

        <mijnui:card.content>
            <div class="flex items-center gap-4">
                <mijnui:label>Use Reducing Conditions</mijnui:label>
                <mijnui:toggle wire:model="formData.misc.use_reducing_conditions" x-on:change="open = !open" />
            </div>

            <template x-if="open">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach (['reduction_reservoir', 'additive_reservoir_a', 'additive_reservoir_b', 'additive_reservoir_c'] as $res)
                        <div class="w-full">
                            <mijnui:label>{{ ucwords(str_replace('_', ' ', $res)) }}</mijnui:label>
                            <div class="flex items-center gap-4">
                                <div class="flex-1">
                                    <mijnui:input type="number" wire:model="formData.misc.{{ $res }}" />
                                </div>
                                <mijnui:select wire:model="formData.misc.{{ $res . '_unit' }}">
                                    <mijnui:select.option value="µL">µL</mijnui:select.option>
                                    <mijnui:select.option value="mL">mL</mijnui:select.option>
                                </mijnui:select>
                            </div>
                        </div>
                    @endforeach
                </div>
            </template>
        </mijnui:card.content>
    </mijnui:card>


    <mijnui:card>
        <mijnui:card.header>Desired Final Product</mijnui:card.header>
        <mijnui:card.content class="grid grid-cols-2 gap-4">
            <mijnui:input label="Desired Final Concentration" placeholder="Concentration" />
        </mijnui:card.content>


        <mijnui:card.footer>
            <mijnui:button type="submit" color="primary" has-loading>Next</mijnui:button>
        </mijnui:card.footer>

    </mijnui:card>


</form>
