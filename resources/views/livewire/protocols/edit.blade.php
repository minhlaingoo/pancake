<div>
    <form wire:submit.prevent="updateProtocol">
        <div class="space-y-4">
            <div class="space-y-2">
                <x-alert />
                <mijnui:card>
                    <mijnui:card.header>
                        <mijnui:card.title class="text-lg font-semibold">
                            Edit Protocol
                        </mijnui:card.title>
                    </mijnui:card.header>
                    <mijnui:card.content>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <mijnui:input label="Sample ID" wire:model="sample_id" disabled />
                            <div class="w-full space-y-1">
                                <p class="text-sm">Description</p>
                                <textarea
                                    class="border-input disabled:opacity-disabled flex min-h-[80px] w-full rounded-md border bg-transparent px-3 py-2 text-sm placeholder:text-muted-text focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring disabled:cursor-not-allowed"
                                    placeholder="Description about protocol" wire:model="description"></textarea>
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
                        <!-- Volume -->
                        <div class="w-full">
                            <mijnui:label>Volume</mijnui:label>
                            <div class="flex items-center gap-4">
                                <div class="flex-1">
                                    <mijnui:input wire:model="formData.mAb.volume" type="number" />
                                </div>
                                <select wire:model="formData.mAb.volume_unit" class="border rounded px-2 py-1">
                                    <option value="µL">µL</option>
                                    <option value="mL">mL</option>
                                </select>
                            </div>
                        </div>
                        
                        <!-- Concentration -->
                        <div class="w-full">
                            <mijnui:label>Concentration</mijnui:label>
                            <div class="flex items-center gap-4">
                                <div class="flex-1">
                                    <mijnui:input wire:model="formData.mAb.concentration" type="number" />
                                </div>
                                <select wire:model="formData.mAb.concentration_unit" class="border rounded px-2 py-1">
                                    <option value="mg/mL">mg/mL</option>
                                    <option value="µg/mL">µg/mL</option>
                                </select>
                            </div>
                        </div>

                        <!-- Molecular Weight -->
                        <mijnui:input type="number" wire:model="formData.mAb.molecular_weight" label="Molecular Weight (kDa)" required />
                        
                        <!-- Molar Absorbing Coefficient -->
                        <mijnui:input type="number" wire:model="formData.mAb.molar_absorbing_coefficient" label="Molar Absorbing Coefficient" required />
                    </mijnui:card.content>
                </mijnui:card>

                <!-- Payload Card -->
                <mijnui:card>
                    <mijnui:card.header>
                        <mijnui:card.title class="text-lg font-semibold">Payload Parameters</mijnui:card.title>
                    </mijnui:card.header>
                    <mijnui:card.content class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Volume Available -->
                        <div class="w-full">
                            <mijnui:label>Volume Available</mijnui:label>
                            <div class="flex items-center gap-4">
                                <div class="flex-1">
                                    <mijnui:input wire:model="formData.payload.volume_available" type="number" required />
                                </div>
                                <select wire:model="formData.payload.volume_available_unit" class="border rounded px-2 py-1">
                                    <option value="µL">µL</option>
                                    <option value="mL">mL</option>
                                </select>
                            </div>
                        </div>

                        <!-- Concentration -->
                        <div class="w-full">
                            <mijnui:label>Concentration</mijnui:label>
                            <div class="flex items-center gap-4">
                                <div class="flex-1">
                                    <mijnui:input wire:model="formData.payload.concentration" type="number" required />
                                </div>
                                <select wire:model="formData.payload.concentration_unit" class="border rounded px-2 py-1">
                                    <option value="mg/mL">mg/mL</option>
                                    <option value="µg/mL">µg/mL</option>
                                </select>
                            </div>
                        </div>

                        <!-- Additional Payload Fields -->
                        <mijnui:input type="number" wire:model="formData.payload.molecular_weight" label="Molecular Weight (kDa)" required />
                        <mijnui:input type="number" wire:model="formData.payload.molar_equivalence" label="Molar Equivalence" required />

                        <div class="w-full">
                            <mijnui:label>Volume to Add</mijnui:label>
                            <div class="flex items-center gap-4">
                                <div class="flex-1">
                                    <mijnui:input type="number" wire:model="formData.mAb.volume_to_add" />
                                </div>
                                <select wire:model="formData.mAb.volume_to_add_unit" class="border rounded px-2 py-1">
                                    <option value="µL">µL</option>
                                    <option value="mL">mL</option>
                                </select>
                            </div>
                        </div>

                        <mijnui:input type="number" wire:model="formData.payload.molar_absorbing_coefficient" label="Molar Absorbing Coefficient" required />
                    </mijnui:card.content>
                </mijnui:card>

                <!-- Miscellaneous Card -->
                <mijnui:card x-data="{ open: formData.misc.use_reducing_conditions }">
                    <mijnui:card.header>
                        <mijnui:card.title class="text-lg font-semibold">Miscellaneous Parameters</mijnui:card.title>
                    </mijnui:card.header>
                    <mijnui:card.content class="space-y-4">
                        <mijnui:toggle wire:model="formData.misc.use_reducing_conditions" label="Use Reducing Conditions" />
                        
                        <div x-show="open" class="grid grid-cols-1 md:grid-cols-2  gap-4">
                            <!-- Reduction Reservoir -->
                            <div class="w-full">
                                <mijnui:label>Reduction Reservoir</mijnui:label>
                                <div class="flex items-center gap-4">
                                    <div class="flex-1">
                                        <mijnui:input wire:model="formData.misc.reduction_reservoir" type="number" />
                                    </div>
                                    <select wire:model="formData.misc.reduction_reservoir_unit" class="border rounded px-2 py-1">
                                        <option value="µL">µL</option>
                                        <option value="mL">mL</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Additive Reservoirs -->
                            <div class="w-full">
                                <mijnui:label>Additive Reservoir A</mijnui:label>
                                <div class="flex items-center gap-4">
                                    <div class="flex-1">
                                        <mijnui:input wire:model="formData.misc.additive_reservoir_a" type="number" />
                                    </div>
                                    <select wire:model="formData.misc.additive_reservoir_a_unit" class="border rounded px-2 py-1">
                                        <option value="µL">µL</option>
                                        <option value="mL">mL</option>
                                    </select>
                                </div>
                            </div>

                            <div class="w-full">
                                <mijnui:label>Additive Reservoir B</mijnui:label>
                                <div class="flex items-center gap-4">
                                    <div class="flex-1">
                                        <mijnui:input wire:model="formData.misc.additive_reservoir_b" type="number" />
                                    </div>
                                    <select wire:model="formData.misc.additive_reservoir_b_unit" class="border rounded px-2 py-1">
                                        <option value="µL">µL</option>
                                        <option value="mL">mL</option>
                                    </select>
                                </div>
                            </div>

                            <div class="w-full">
                                <mijnui:label>Additive Reservoir C</mijnui:label>
                                <div class="flex items-center gap-4">
                                    <div class="flex-1">
                                        <mijnui:input wire:model="formData.misc.additive_reservoir_c" type="number" />
                                    </div>
                                    <select wire:model="formData.misc.additive_reservoir_c_unit" class="border rounded px-2 py-1">
                                        <option value="µL">µL</option>
                                        <option value="mL">mL</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </mijnui:card.content>
                </mijnui:card>

                <mijnui:card>
                    <mijnui:card.header>Desired Final Product</mijnui:card.header>
                    <mijnui:card.content class="grid grid-cols-2 gap-4">
                        <mijnui:input label="Desired Final Concentration" placeholder="Concentration" />
                    </mijnui:card.content>

                    <mijnui:card.footer class="flex justify-between">
                        <mijnui:button 
                            wire:click="updateProtocol" 
                            color="primary">
                            Update
                        </mijnui:button>
                    </mijnui:card.footer>
                </mijnui:card>
            </div>
        </div>
    </form>
</div>