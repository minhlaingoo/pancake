@php
    $phaseCount = count($phases);
    $elapsed = $onGoingTime;
    $totalSec = array_sum(array_map(fn($p) => (int) ($p['duration'] ?? 0) * (int) ($p['loop'] ?? 1), $phases));
    $progressPct = $totalSec > 0 ? min(100, round(($elapsed / $totalSec) * 100)) : 0;

    $statusColor = 'secondary';
    $statusLabel = 'Not Started';
    if ($protocolProcess->ended_at) {
        $statusColor = 'success';
        $statusLabel = 'Finished';
    } elseif (count($data)) {
        $statusColor = 'info';
        $statusLabel = 'Processing';
    }
@endphp

<div wire:key="protocol-process-{{ $protocolProcess->id }}">

    {{-- Live Polling (only when running) --}}
    @if ($startTime && !$protocolProcess->ended_at)
        <div wire:poll.2s="refreshProtocolProcessData"></div>
    @endif

    {{-- Header --}}
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-foreground tracking-tight">Protocol Processing Monitor</h2>
        <p class="text-muted-foreground text-sm">Real-time protocol execution tracking</p>
    </div>

    {{-- Status Cards Row --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">

        {{-- Status Badge Card --}}
        <mijnui:card class="p-5 flex flex-col gap-2">
            <span class="text-xs font-semibold text-muted-foreground uppercase tracking-wider">Status</span>
            <mijnui:badge color="{{ $statusColor }}">{{ $statusLabel }}</mijnui:badge>
        </mijnui:card>

        {{-- Start Time Card --}}
        <mijnui:card class="p-5 flex flex-col gap-2">
            <span class="text-xs font-semibold text-muted-foreground uppercase tracking-wider">Started</span>
            <span class="text-sm font-medium text-foreground">{{ $startTime ?? '—' }}</span>
        </mijnui:card>

        {{-- Finished Time Card --}}
        <mijnui:card class="p-5 flex flex-col gap-2">
            <span class="text-xs font-semibold text-muted-foreground uppercase tracking-wider">Finished</span>
            <span class="text-sm font-medium text-foreground">{{ $protocolProcess->ended_at ?? '—' }}</span>
        </mijnui:card>
    </div>

    {{-- Setup Overview Card --}}
    @php
        $setup = json_decode($protocol->value, true) ?: [];
    @endphp
    @if (!empty($setup))
        <mijnui:card class="mb-6">
            <mijnui:card.header class="px-5 pt-5 pb-0">
                <mijnui:card.title class="text-sm font-semibold text-foreground uppercase tracking-wider">Setup Overview
                </mijnui:card.title>
            </mijnui:card.header>
            <mijnui:card.content class="p-5">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                    <div>
                        <span class="text-xs font-semibold text-muted-foreground uppercase tracking-wider block mb-1">mAb
                            Volume</span>
                        <span class="text-sm font-bold text-foreground">{{ $setup['mAb']['volume'] ?? 0 }} mL</span>
                    </div>
                    <div>
                        <span
                            class="text-xs font-semibold text-muted-foreground uppercase tracking-wider block mb-1">Payload
                            Volume</span>
                        <span class="text-sm font-bold text-foreground">{{ $setup['payload']['volume_to_add'] ?? 0 }}
                            mL</span>
                    </div>
                    <div>
                        <span
                            class="text-xs font-semibold text-muted-foreground uppercase tracking-wider block mb-1">Reduction
                            Reservoir</span>
                        <span class="text-sm font-bold text-foreground">{{ $setup['misc']['reduction_reservoir'] ?? 0 }}
                            mL</span>
                    </div>
                    <div>
                        <span class="text-xs font-semibold text-muted-foreground uppercase tracking-wider block mb-1">Final
                            Volume</span>
                        <span class="text-sm font-bold text-foreground">{{ $setup['misc']['desired_final_volume'] ?? 0 }}
                            mL</span>
                    </div>
                </div>
            </mijnui:card.content>
        </mijnui:card>
    @endif

    {{-- Progress Bar (only while running) --}}
    @if ($startTime && !$protocolProcess->ended_at)
        <mijnui:card class="p-5 mb-6">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-semibold text-muted-foreground uppercase tracking-wider">Progress</span>
                <span class="text-xs font-bold text-foreground">{{ $progressPct }}%</span>
            </div>
            <div class="w-full h-2.5 bg-muted rounded-full overflow-hidden">
                <div class="h-full bg-primary rounded-full transition-all duration-500 ease-out"
                    style="width: {{ $progressPct }}%"></div>
            </div>
            <div class="flex items-center justify-between mt-2 text-xs text-muted-foreground">
                <span>Elapsed: {{ gmdate('H:i:s', $elapsed) }}</span>
                <span>Total: {{ gmdate('H:i:s', $totalSec) }}</span>
            </div>
        </mijnui:card>
    @endif

    {{-- Control Button --}}
    @if (!$protocolProcess->ended_at)
        <div class="mb-6">
            <mijnui:button wire:click="toggleProtocolProcessing" wire:target="toggleProtocolProcessing" has-loading
                color="{{ $protocolProcessing ? 'warning' : 'primary' }}">
                <i class="fas {{ $protocolProcessing ? 'fa-pause' : 'fa-play' }} mr-2"></i>
                {{ $protocolProcessing ? 'Pause' : 'Start' }}
            </mijnui:button>
        </div>
    @endif

    {{-- Chart Card --}}
    <mijnui:card class="mb-6">
        <mijnui:card.header class="px-5 pt-5 pb-0">
            <mijnui:card.title class="text-sm font-semibold text-foreground uppercase tracking-wider">Live Chart
            </mijnui:card.title>
        </mijnui:card.header>
        <mijnui:card.content class="p-5">
            <div x-data="protocolChart(@js($phases), @js($data))" x-init="init()" class="w-full min-h-64 max-h-96 ">
                <canvas x-ref="chartCanvas" class="w-full h-full"></canvas>
            </div>

            {{-- Legend --}}
            <div class="mt-4 flex items-center gap-6">
                <div class="flex items-center gap-2 text-sm text-foreground">
                    <span class="inline-block w-3 h-3 rounded-sm bg-danger"></span>
                    Temperature (°C)
                </div>
                <div class="flex items-center gap-2 text-sm text-foreground">
                    <span class="inline-block w-3 h-3 rounded-sm bg-info"></span>
                    Volume (L)
                </div>
                <div class="flex items-center gap-2 text-sm text-foreground">
                    <span class="inline-block w-3 h-3 rounded-sm bg-success"></span>
                    Phase Marker
                </div>
            </div>
        </mijnui:card.content>
    </mijnui:card>

    {{-- Phases Timeline --}}
    @if (count($phases))
        <mijnui:card class="mb-6">
            <mijnui:card.header class="px-5 pt-5 pb-0">
                <mijnui:card.title class="text-sm font-semibold text-foreground uppercase tracking-wider">Protocol Phases
                </mijnui:card.title>
            </mijnui:card.header>
            <mijnui:card.content class="p-5">
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3">
                    @foreach ($phases as $index => $phase)
                        <div class="flex items-start gap-3 p-3 rounded-xl bg-secondary border border-border">
                            <div
                                class="shrink-0 w-7 h-7 rounded-full bg-primary text-primary-foreground flex items-center justify-center text-xs font-bold">
                                {{ $index + 1 }}
                            </div>
                            <div class="min-w-0">
                                <p class="text-sm font-medium text-foreground truncate">
                                    {{ $phase['label'] ?? 'Phase ' . ($index + 1) }}
                                </p>
                                <p class="text-xs text-muted-foreground">
                                    {{ $phase['duration'] }}s × {{ $phase['loop'] ?? 1 }}
                                    loop{{ ($phase['loop'] ?? 1) > 1 ? 's' : '' }}
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </mijnui:card.content>
        </mijnui:card>
    @endif

    {{-- Collapsible Raw Data (Debug) --}}
    <mijnui:card>
        <mijnui:card.content class="p-5">
            <div x-data="{ showRaw: false }">
                <button @click="showRaw = !showRaw"
                    class="flex items-center gap-2 text-sm font-semibold text-muted-foreground hover:text-foreground transition-colors cursor-pointer uppercase tracking-wider">
                    <i class="fas fa-chevron-right text-[10px] transition-transform duration-200"
                        :class="showRaw ? 'rotate-90' : ''"></i>
                    Raw Data ({{ count($data) }} entries)
                </button>

                <div x-show="showRaw" x-collapse>
                    <div class="mt-4 max-h-64 overflow-y-auto space-y-1">
                        @forelse ($data as $d)
                            <pre class="text-xs p-2 rounded-lg bg-muted text-muted-foreground font-mono">@json($d)</pre>
                        @empty
                            <p class="text-sm text-muted-foreground italic">No data yet.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </mijnui:card.content>
    </mijnui:card>
</div>

{{-- Chart.js via @push so it lands in the layout's scripts stack --}}
@once
    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns"></script>
    @endpush
@endonce

@push('scripts')
    <script>
        // Phase label plugin — draws phase badges at the bottom of the chart area
        const phaseLabelPlugin = {
            id: 'phaseLabelPlugin',
            afterDatasetsDraw(chart, args, options) {
                const { ctx, chartArea: { bottom, right }, scales: { x } } = chart;
                if (!options || !options.phases) return;

                const phases = options.phases.sort((a, b) => a.timestamp - b.timestamp);
                const rootStyle = getComputedStyle(document.documentElement);
                const successColor = rootStyle.getPropertyValue('--success').trim();
                const fgColor = rootStyle.getPropertyValue('--foreground').trim();

                ctx.save();
                ctx.textAlign = 'center';
                ctx.textBaseline = 'middle';
                ctx.font = '11px "Outfit", sans-serif';

                phases.forEach((p, idx) => {
                    const currentX = x.getPixelForValue(p.timestamp * 1000);
                    const nextX = idx < phases.length - 1
                        ? x.getPixelForValue(phases[idx + 1].timestamp * 1000)
                        : right;

                    const midX = (currentX + nextX) / 2;
                    const label = `${p.label}${p.loop_index == 1 ? '' : '\n(loop ' + p.loop_index + ')'}`;
                    const lines = label.split('\n');

                    const paddingX = 6;
                    const paddingY = 3;
                    const lineHeight = 14;
                    const textWidth = Math.max(...lines.map(l => ctx.measureText(l).width));
                    const rectWidth = textWidth + paddingX * 2;
                    const rectHeight = lineHeight * lines.length + paddingY * 2;
                    const rectX = midX - rectWidth / 2;
                    const rectY = bottom - rectHeight - 5;
                    const radius = 6;

                    // Background badge
                    ctx.fillStyle = successColor ? `hsl(${successColor} / 0.15)` : 'rgba(0,128,0,0.15)';
                    ctx.beginPath();
                    ctx.moveTo(rectX + radius, rectY);
                    ctx.lineTo(rectX + rectWidth - radius, rectY);
                    ctx.quadraticCurveTo(rectX + rectWidth, rectY, rectX + rectWidth, rectY + radius);
                    ctx.lineTo(rectX + rectWidth, rectY + rectHeight - radius);
                    ctx.quadraticCurveTo(rectX + rectWidth, rectY + rectHeight, rectX + rectWidth - radius, rectY + rectHeight);
                    ctx.lineTo(rectX + radius, rectY + rectHeight);
                    ctx.quadraticCurveTo(rectX, rectY + rectHeight, rectX, rectY + rectHeight - radius);
                    ctx.lineTo(rectX, rectY + radius);
                    ctx.quadraticCurveTo(rectX, rectY, rectX + radius, rectY);
                    ctx.closePath();
                    ctx.fill();

                    // Text
                    ctx.fillStyle = successColor ? `hsl(${successColor})` : 'green';
                    lines.forEach((line, i) => {
                        ctx.fillText(line, midX, rectY + paddingY + (i + 0.5) * lineHeight);
                    });
                });

                ctx.restore();
            }
        };

        Chart.register(phaseLabelPlugin);

        function protocolChart(phases, initialData) {
            return {
                data: initialData,
                chart: null,

                renderChart() {
                    const canvas = this.$refs.chartCanvas;
                    if (!canvas) return;

                    // Destroy any existing chart
                    if (this.chart instanceof Chart) {
                        this.chart.destroy();
                    }

                    // Read theme colors
                    const rootStyle = getComputedStyle(document.documentElement);
                    const dangerColor = rootStyle.getPropertyValue('--danger').trim();
                    const infoColor = rootStyle.getPropertyValue('--info').trim();
                    const successColor = rootStyle.getPropertyValue('--success').trim();
                    const fgColor = rootStyle.getPropertyValue('--foreground').trim();
                    const mutedFgColor = rootStyle.getPropertyValue('--muted-foreground').trim();
                    const borderColor = rootStyle.getPropertyValue('--border').trim();

                    this.chart = new Chart(canvas.getContext('2d'), {
                        type: 'line',
                        data: { datasets: [] },
                        options: {
                            animation: false,
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                phaseLabelPlugin: { phases: [] },
                                legend: { display: false }
                            },
                            scales: {
                                x: {
                                    type: 'time',
                                    time: {
                                        unit: 'second',
                                        tooltipFormat: 'yyyy-MM-dd HH:mm:ss'
                                    },
                                    title: {
                                        display: true,
                                        text: 'Time',
                                        color: mutedFgColor ? `hsl(${mutedFgColor})` : '#888',
                                        font: { family: '"Outfit", sans-serif' }
                                    },
                                    ticks: {
                                        maxTicksLimit: 20,
                                        color: mutedFgColor ? `hsl(${mutedFgColor})` : '#888',
                                        font: { family: '"Outfit", sans-serif' }
                                    },
                                    grid: {
                                        color: borderColor ? `hsl(${borderColor} / 0.3)` : 'rgba(128,128,128,0.15)'
                                    }
                                },
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        color: mutedFgColor ? `hsl(${mutedFgColor})` : '#888',
                                        font: { family: '"Outfit", sans-serif' }
                                    },
                                    grid: {
                                        color: borderColor ? `hsl(${borderColor} / 0.3)` : 'rgba(128,128,128,0.15)'
                                    }
                                }
                            }
                        }
                    });

                    // Store theme colors for updateChart
                    this._colors = {
                        danger: dangerColor ? `hsl(${dangerColor})` : 'red',
                        info: infoColor ? `hsl(${infoColor})` : 'blue',
                        success: successColor ? `hsl(${successColor})` : 'green',
                    };

                    this.updateChart();
                },

                init() {
                    this.renderChart();

                    // Livewire v3: listen for the dispatched event
                    Livewire.on('protocolDataUpdated', (eventData) => {
                        this.data = Array.isArray(eventData) ? eventData[0] ?? eventData : eventData;
                        this.updateChart();
                    });

                    // Also update on any Livewire commit (covers poll refreshes)
                    Livewire.hook('commit', ({ component, respond }) => {
                        respond(() => {
                            const freshData = component.snapshot?.data?.data;
                            if (freshData) {
                                this.data = JSON.parse(JSON.stringify(freshData));
                                this.updateChart();
                            }
                        });
                    });
                },

                updateChart() {
                    if (!this.chart) return;

                    const measurements = this.data.filter(d => d.type === 'measurement');
                    const phaseStarts = this.data.filter(d => d.type === 'phase_start');
                    const colors = this._colors || { danger: 'red', info: 'blue', success: 'green' };

                    const maxY = Math.max(
                        ...measurements.map(m => m.temperature || 0),
                        ...measurements.map(m => m.volume || 0),
                        100
                    );

                    const datasets = [
                        {
                            label: 'Temperature',
                            borderColor: colors.danger,
                            backgroundColor: colors.danger,
                            fill: false,
                            tension: 0.3,
                            pointRadius: 2,
                            borderWidth: 2,
                            data: measurements.map(d => ({
                                x: new Date(d.timestamp * 1000),
                                y: d.temperature
                            }))
                        },
                        {
                            label: 'Volume',
                            borderColor: colors.info,
                            backgroundColor: colors.info,
                            fill: false,
                            tension: 0.3,
                            pointRadius: 2,
                            borderWidth: 2,
                            data: measurements.map(d => ({
                                x: new Date(d.timestamp * 1000),
                                y: d.volume
                            }))
                        }
                    ];

                    // Vertical phase markers
                    phaseStarts.forEach(p => {
                        datasets.push({
                            label: `${p.label} ${p.loop_index}`,
                            borderColor: colors.success,
                            borderWidth: 1,
                            borderDash: [4, 4],
                            pointRadius: 0,
                            showLine: true,
                            data: [
                                { x: new Date(p.timestamp * 1000), y: 0 },
                                { x: new Date(p.timestamp * 1000), y: maxY }
                            ]
                        });
                    });

                    this.chart.data.datasets = datasets;
                    this.chart.options.plugins.phaseLabelPlugin.phases = phaseStarts;
                    this.chart.update('none');
                }
            }
        }
    </script>
@endpush