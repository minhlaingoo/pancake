<div class="max-w-6xl mx-auto space-y-4 flex flex-row sm:flex-col">
    <div class="bg-white w-fit  sm:w-full px-2 py-2 sm:px-0 sm:py-4 shadow-lg rounded">
        <mijnui:stepper variant="default" size="default" class="mx-auto">

            @php $last = count($steps); @endphp

            @for ($i = 1; $i <= $last; $i++)
                <mijnui:stepper.item wire:click="setStep({{ $i }})"
                    state="{{ $i == $current_step ? 'current' : 'default' }}" value="{{ $i }}"
                    :is-last="$last == $i ? true : false"/>
            @endfor

        </mijnui:stepper>
    </div>

    <div class="bg-white self-start w-full py-4 shadow-lg rounded">
        <h2 class="text-xl mb-4 font-medium text-center">{{ $steps[$current_step - 1]['title'] }}</h2>
        <div class="mx-auto w-fit space-y-4">

            @foreach ($steps[$current_step - 1]['step'] as $step)
                <div class="flex items-center gap-2">
                    @if ($step['is_finished'])
                        <i
                            class="fas fa-check text-xs text-white rounded size-5 bg-success flex items-center justify-center"></i>
                    @else
                        <i
                            class="fas fa-clock text-xs text-white rounded size-5 bg-yellow-500 flex items-center justify-center"></i>
                    @endif
                    <p class="text-gray-600">{{ $step['title'] }}</p>
                </div>
            @endforeach
        </div>

    </div>
</div>
