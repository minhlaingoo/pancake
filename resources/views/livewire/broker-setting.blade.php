<form wire:submit.prevent="save" class="space-y-4">
    <div>
        <mijnui:breadcrumbs>
            <mijnui:breadcrumbs.item isLast>Broker Settings</mijnui:breadcrumbs.item>
        </mijnui:breadcrumbs>
        <h2 class="text-2xl font-semibold">Broker Settings</h2>
    </div>

    <x-alert />

    <mijnui:card>
        <mijnui:card.header>
            <mijnui:card.title class="text-lg font-semibold">Connection</mijnui:card.title>
        </mijnui:card.header>
        <mijnui:card.content>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">

                <mijnui:input label="Broker URL" type="text" wire:model="broker_url" required
                    placeholder="e.g. mqtt.example.com" />
                @error('broker_url')
                    <p class="text-destructive text-sm -mt-3">{{ $message }}</p>
                @enderror

                <mijnui:input label="Port" type="number" wire:model="broker_port"
                    placeholder="e.g. 1883" />
                @error('broker_port')
                    <p class="text-destructive text-sm -mt-3">{{ $message }}</p>
                @enderror

                <mijnui:input label="Protocol Version" type="text" wire:model="broker_protocol_version"
                    placeholder="e.g. 4" />

                <div class="lg:col-span-3">
                    <mijnui:input label="Keep Alive Interval (seconds)" type="number" wire:model="broker_keep_alive_interval"
                        min="0" placeholder="e.g. 60" />
                </div>

                <div class="flex items-center gap-2">
                    <mijnui:switch wire:model="broker_clean_session" />
                    <mijnui:label class="mb-0">Start with clean session</mijnui:label>
                </div>
            </div>
        </mijnui:card.content>
    </mijnui:card>

    <mijnui:card>
        <mijnui:card.header>
            <mijnui:card.title class="text-lg font-semibold">Authentication</mijnui:card.title>
        </mijnui:card.header>
        <mijnui:card.content class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2 md:w-1/2">
                    <mijnui:label>Authentication Type</mijnui:label>
                    <mijnui:select wire:model.live="broker_auth_type">
                        @foreach ($broker_auth_types as $key => $method)
                            <mijnui:select.option value="{{ $key }}">
                                {{ $method }}
                            </mijnui:select.option>
                        @endforeach
                    </mijnui:select>
                </div>

                @if ($broker_auth_type == 'auth')
                    <mijnui:input label="Username" type="text" wire:model="broker_username" required
                        autocomplete="username" placeholder="MQTT username" />
                    <mijnui:input label="Password" type="password" wire:model="broker_password" viewable
                        autocomplete="current-password" placeholder="MQTT password" />
                @endif

                @if($broker_auth_type == 'tls')
                    <div class="md:col-span-2 space-y-4">
                        <div class="w-full">
                            <mijnui:label>Client Certificate (*.crt, *.pem)</mijnui:label>
                            <div wire:loading.class="opacity-50" wire:target="client_cert">
                                <input type="file" wire:model="client_cert" accept=".crt,.pem"
                                    class="block w-full text-sm text-muted-foreground
                                        file:mr-4 file:py-2 file:px-4
                                        file:rounded-md file:border-0
                                        file:text-sm file:font-semibold
                                        file:bg-primary file:text-primary-foreground
                                        hover:file:bg-primary/80 file:cursor-pointer" />
                            </div>
                            @error('client_cert') <span class="text-destructive text-sm">{{ $message }}</span> @enderror
                            @if($client_cert)
                                <p class="text-sm text-success mt-1">File selected: {{ $client_cert->getClientOriginalName() }}</p>
                            @endif
                        </div>

                        <div class="w-full">
                            <mijnui:label>Client Key (*.key, *.pem)</mijnui:label>
                            <div wire:loading.class="opacity-50" wire:target="client_key">
                                <input type="file" wire:model="client_key" accept=".key,.pem"
                                    class="block w-full text-sm text-muted-foreground
                                        file:mr-4 file:py-2 file:px-4
                                        file:rounded-md file:border-0
                                        file:text-sm file:font-semibold
                                        file:bg-primary file:text-primary-foreground
                                        hover:file:bg-primary/80 file:cursor-pointer" />
                            </div>
                            @error('client_key') <span class="text-destructive text-sm">{{ $message }}</span> @enderror
                            @if($client_key)
                                <p class="text-sm text-success mt-1">File selected: {{ $client_key->getClientOriginalName() }}</p>
                            @endif
                        </div>

                        <div class="w-full">
                            <mijnui:label>CA Certificate (*.crt, *.pem, *.cer)</mijnui:label>
                            <div wire:loading.class="opacity-50" wire:target="ca_cert">
                                <input type="file" wire:model="ca_cert"
                                    accept=".crt,.CRT,.pem,.PEM,.cer,.CER"
                                    class="block w-full text-sm text-muted-foreground
                                        file:mr-4 file:py-2 file:px-4
                                        file:rounded-md file:border-0
                                        file:text-sm file:font-semibold
                                        file:bg-primary file:text-primary-foreground
                                        hover:file:bg-primary/80 file:cursor-pointer" />
                            </div>
                            @error('ca_cert')
                                <span class="text-destructive text-sm">{{ $message }}</span>
                            @enderror
                            @if($ca_cert)
                                <p class="text-sm text-success mt-1">File selected: {{ $ca_cert->getClientOriginalName() }}</p>
                            @endif
                        </div>
                    </div>
                @endif
            </div>

            <div class="flex items-center gap-2">
                <mijnui:switch wire:model="broker_can_publish" />
                <mijnui:label class="mb-0">Allow sending commands to devices</mijnui:label>
            </div>

            <div class="flex items-center gap-2">
                <mijnui:switch wire:model="enable_log" />
                <mijnui:label class="mb-0">Log MQTT messages for debugging</mijnui:label>
            </div>

        </mijnui:card.content>
    </mijnui:card>

    <mijnui:card>
        <mijnui:card.header>
            <mijnui:card.title class="text-lg font-semibold">Subscription</mijnui:card.title>
        </mijnui:card.header>
        <mijnui:card.content class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                <mijnui:input label="Subscribed Topic" type="text" wire:model="subscribe_topic"
                    placeholder="e.g. devices/#" />
                <mijnui:select label="QoS Service Level" wire:model="subscribe_qos">
                    <mijnui:select.option value="0">0 — At most once</mijnui:select.option>
                    <mijnui:select.option value="1">1 — At least once</mijnui:select.option>
                    <mijnui:select.option value="2">2 — Exactly once</mijnui:select.option>
                </mijnui:select>

            </div>

            <div class="flex items-center gap-2">
                <mijnui:switch wire:model="subscribe_retain" />
                <mijnui:label class="mb-0">Receive retained messages on connect</mijnui:label>
            </div>

            <mijnui:button type="submit" color="primary" has-loading>Save Broker Settings</mijnui:button>
        </mijnui:card.content>
    </mijnui:card>

    <script>
        window.addEventListener('refresh-page', () => {
            window.location.reload();
        });
    </script>

</form>
