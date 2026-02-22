<mijnui:card >
    <mijnui:card.header>
        <mijnui:card.title class="md:text-2xl font-bold text-gray-800">Activity Log #{{ $log->id }}</mijnui:card.title>
    </mijnui:card.header>

    <mijnui:card.content>

        <table class="w-full">
            @foreach($log_info as $key => $value)
                <tr class="border-b border-gray-200 hover:bg-gray-50 transition-colors duration-200">
                    <td class="py-3 text-sm md:text-base font-semibold text-gray-700">{{$key}}</td>
                    <td class="w-8 text-center text-gray-500">:</td>
                    <td class="py-3 text-sm md:text-base font-medium text-gray-600">{{$value}}</td>
                </tr>
            @endforeach
        </table>

    </mijnui:card.content>
</mijnui:card>
