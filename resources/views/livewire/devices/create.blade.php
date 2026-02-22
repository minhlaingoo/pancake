<form wire:submit="store">

    <div>
        <mijnui:breadcrumbs>
            <mijnui:breadcrumbs.item href="{{ route('devices.index') }}">Devices</mijnui:breadcrumbs.item>
            <mijnui:breadcrumbs.item isLast>Create</mijnui:breadcrumbs.item>
        </mijnui:breadcrumbs>
        <h2 class="text-2xl font-semibold">Devices Create</h2>
    </div>
    <mijnui:card>
        <mijnui:card.content class="grid grid-cols-2 gap-4">

            <mijnui:input wire:model="name" label="Device Name" placeholder="e.g. ChemLab" required />
            <mijnui:input wire:model="model" label="Model Name" placeholder="e.g. IoT3201" required />
            <mijnui:input wire:model="ip" label="Device Ip/Domain" placeholder="e.g. 163.21.62.133" required />
            <mijnui:input wire:model="port" type="number" label="Device Port" placeholder="e.g. 8080" required />

        </mijnui:card.content>

        <mijnui:card.footer>
            <mijnui:button type="submit" color="primary" has-loading>
                Create
            </mijnui:button>
        </mijnui:card.footer>
    </mijnui:card>


</form>