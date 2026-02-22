<form wire:submit.prevent="save" class="space-y-2">
    <mijnui:card>
        <x-alert />
    
        <mijnui:card.header class="text-2xl font-semibold">Broker Setting</mijnui:card.header>
        <mijnui:card.content>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">

                <mijnui:input label="Broker URL" type="text" wire:model="broker_url" required/>
                <mijnui:input label="Port" type="text" wire:model="broker_port" />
                <mijnui:input label="Protocol Version" type="text" wire:model="broker_protocol_version" />
                <div class="lg:col-span-3">
                    <mijnui:input label="Keep Alive Interval" type="number" wire:model="broker_keep_alive_interval" />
                </div>

                <div class="flex items-center gap-2">
                    <mijnui:switch wire:model="broker_clean_session" />
                    <mijnui:label>Must Clean Session</mijnui:label>
                </div>
            </div>

        </mijnui:card.content>
    </mijnui:card>

    <mijnui:card>
        <mijnui:card.header class="text-2xl font-semibold">Authentication Setting</mijnui:card.header>
        <mijnui:card.content class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2 w-1/2">

                    <div class="w-full">
                        <mijnui:label>Authentication Type</mijnui:label>
                        <br />
                        <mijnui:select wire:model.live="broker_auth_type">
                            @foreach ($broker_auth_types as $key => $method)
                                <mijnui:select.option value="{{ $key }}">
                                    {{ $method }}
                                </mijnui:select.option>
                            @endforeach
                        </mijnui:select>
                    </div>
                </div>

                @if ($broker_auth_type == 'auth')
                    <mijnui:input label="Username" type="text" wire:model="broker_username" />
                    <mijnui:input label="Password" type="password" wire:model="broker_password" viewable />
                @endif

                @if($broker_auth_type == 'tls')
                    <div class="space-y-4">
                        <div class="w-full">
                            <mijnui:label>Client Certificate (*.crt)</mijnui:label>
                            <input type="file" wire:model="client_cert" accept=".crt,.pem" 
                                class="block w-full text-sm text-slate-500
                                    file:mr-4 file:py-2 file:px-4
                                    file:rounded-md file:border-0
                                    file:text-sm file:font-semibold
                                    file:bg-primary file:text-white
                                    hover:file:bg-primary/80"/>
                            @error('client_cert') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            @if($client_cert)
                                <p class="text-sm text-green-600 mt-1">File selected: {{ $client_cert->getClientOriginalName() }}</p>
                            @endif
                        </div>

                        <div class="w-full">
                            <mijnui:label>Client Key (*.key)</mijnui:label>
                            <input type="file" wire:model="client_key" accept=".key,.pem"
                                class="block w-full text-sm text-slate-500
                                    file:mr-4 file:py-2 file:px-4
                                    file:rounded-md file:border-0
                                    file:text-sm file:font-semibold
                                    file:bg-primary file:text-white
                                    hover:file:bg-primary/80"/>
                            @error('client_key') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            @if($client_key)
                                <p class="text-sm text-green-600 mt-1">File selected: {{ $client_key->getClientOriginalName() }}</p>
                            @endif
                        </div>

                        <div class="w-full">
                            <mijnui:label>CA Certificate</mijnui:label>
                            <input type="file" 
                                wire:model="ca_cert" 
                                accept=".crt,.CRT,.pem,.PEM,.cer,.CER" 
                                class="block w-full text-sm text-slate-500
                                    file:mr-4 file:py-2 file:px-4
                                    file:rounded-md file:border-0
                                    file:text-sm file:font-semibold
                                    file:bg-primary file:text-white
                                    hover:file:bg-primary/80"/>
                            @error('ca_cert') 
                                <span class="text-red-500 text-sm">{{ $message }}</span> 
                            @enderror
                            @if($ca_cert)
                                <p class="text-sm text-green-600 mt-1">File selected: {{ $ca_cert->getClientOriginalName() }}</p>
                            @endif
                        </div>
                    </div>
                @endif
            </div>

            <div class="flex items-center gap-2">
                <mijnui:switch wire:model="broker_can_publish" />
                <mijnui:label>Enable Publishing</mijnui:label>
            </div>

            <div class="flex items-center gap-2">
                <mijnui:switch wire:model="enable_log" />
                <mijnui:label>Enable Logging</mijnui:label>
            </div>

        </mijnui:card.content>
    </mijnui:card>

    <mijnui:card>
        <mijnui:card.header class="text-2xl font-semibold">Topic Setting</mijnui:card.header>
        <mijnui:card.content class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                <mijnui:input label="Subscribed Topic" type="text" wire:model="subscribe_topic" />
                <mijnui:select label="QoS Service Level" wire:model="subscribe_qos">
                    <mijnui:select.option value="0">0</mijnui:select.option>
                    <mijnui:select.option value="1">1</mijnui:select.option>
                    <mijnui:select.option value="2">2</mijnui:select.option>
                </mijnui:select>


            </div>

            <div class="flex items-center gap-2">
                <mijnui:switch wire:model="subscribe_retain" />
                <mijnui:label>Retained Message</mijnui:label>
            </div>

            <mijnui:button color="primary" has-loading>Update</mijnui:button>
        </mijnui:card.content>
    </mijnui:card>



    <script>
        window.addEventListener('refresh-page', () => {
            window.location.reload();
        });
    </script>

</form>
