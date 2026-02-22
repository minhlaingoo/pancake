<div>
    <form wire:submit="update">

        <mijnui:card>
            <mijnui:card.header>
                <mijnui:card.title class="text-2xl font-bold text-foreground">Device Component Update
                </mijnui:card.title>
            </mijnui:card.header>

            <mijnui:card.content class="grid grid-cols-2 gap-4">

                <mijnui:input wire:model="name" label="Component Name" placeholder="e.g. temperature checker"
                    required />
                <mijnui:input wire:model="type" label="Component Type" placeholder="e.g. Heat Sensor" required />
                <mijnui:input wire:model="unit" label="Component Unit" placeholder="e.g. *C" required />

            </mijnui:card.content>

            <mijnui:card.footer>

                <mijnui:button type="submit" color="primary" has-loading>Update</mijnui:button>

            </mijnui:card.footer>

        </mijnui:card>

    </form>

</div>