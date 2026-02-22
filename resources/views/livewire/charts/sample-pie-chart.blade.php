@php
    $id = Str::random(10);
@endphp

<div>
    <canvas id="{{ $id }}" />
</div>

@once
    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @endpush
@endonce

@push('scripts')
    <script>
        var chart = new Chart(
            document.getElementById("{{ $id }}"), {
                type: 'pie', // ✅ Changed to 'pie'
                data: {
                    labels: @json($labels),
                    datasets: @json($dataset)
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.label || '';
                                    let value = context.raw || 0;
                                    return `${label}: ${value} ml`;
                                }
                            }
                        }
                    }
                }
            }
        );
    </script>
@endpush
