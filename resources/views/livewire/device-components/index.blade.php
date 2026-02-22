<!-- Table -->
<section id="table" class="">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-2xl font-semibold">Device Components List</h2>
        {{-- @if (checkPermission('role', 'create'))
        <mijnui:button color="primary" href="{{ route('role-permissions.create') }}" wire:navigate>Create
        </mijnui:button>
        @endif --}}
    </div>
    <x-alert />
    <div>
        <mijnui:table>

            <mijnui:table.columns>
                <mijnui:table.column>Name</mijnui:table.column>
                <mijnui:table.column>Component Type</mijnui:table.column>
                <mijnui:table.column>Device Name</mijnui:table.column>
                <mijnui:table.column>Unit</mijnui:table.column>
            </mijnui:table.columns>

            <mijnui:table.rows>
                <mijnui:table.row>
                    <mijnui:table.cell>Heat Device Component</mijnui:table.cell>
                    <mijnui:table.cell>Iot3201</mijnui:table.cell>
                    <mijnui:table.cell>ChemLab</mijnui:table.cell>
                    <mijnui:table.cell>*C</mijnui:table.cell>

                </mijnui:table.row>

            </mijnui:table.rows>
        </mijnui:table>
    </div>
</section>