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
            <div x-data="protocolChart(@js($phases), @js($data))" x-init="init()" class="w-full min-h-64 max-h-96">
                <div x-ref="chartContainer" class="w-full h-full"></div>
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

@script
<script>
    window.protocolChart = function(phases, initialData) {
        return {
            data: initialData,
            svg: null,

            renderChart() {
                const container = this.$refs.chartContainer;
                if (!container || !window.d3) return;

                // Clear previous
                d3.select(container).selectAll('*').remove();

                const rootStyle = getComputedStyle(document.documentElement);
                const dangerColor = rootStyle.getPropertyValue('--danger').trim();
                const infoColor = rootStyle.getPropertyValue('--info').trim();
                const successColor = rootStyle.getPropertyValue('--success').trim();
                const mutedFgColor = rootStyle.getPropertyValue('--muted-foreground').trim();
                const borderColor = rootStyle.getPropertyValue('--border').trim();

                this._colors = {
                    danger: dangerColor ? `hsl(${dangerColor})` : 'red',
                    info: infoColor ? `hsl(${infoColor})` : 'blue',
                    success: successColor ? `hsl(${successColor})` : 'green',
                    muted: mutedFgColor ? `hsl(${mutedFgColor})` : '#888',
                    border: borderColor ? `hsl(${borderColor} / 0.3)` : 'rgba(128,128,128,0.15)',
                };

                const margin = { top: 10, right: 15, bottom: 35, left: 45 };
                const width = container.clientWidth - margin.left - margin.right;
                const height = container.clientHeight - margin.top - margin.bottom;

                this.svg = d3.select(container).append('svg')
                    .attr('width', '100%')
                    .attr('height', '100%')
                    .attr('viewBox', `0 0 ${container.clientWidth} ${container.clientHeight}`)
                    .append('g')
                    .attr('transform', `translate(${margin.left},${margin.top})`);

                this._dims = { width, height, margin };
                this.updateChart();
            },

            init() {
                this.renderChart();

                Livewire.on('protocolDataUpdated', (eventData) => {
                    this.data = Array.isArray(eventData) ? eventData[0] ?? eventData : eventData;
                    this.updateChart();
                });

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
                if (!this.svg) return;
                const { width, height } = this._dims;
                const colors = this._colors;

                const measurements = this.data.filter(d => d.type === 'measurement');
                const phaseStarts = this.data.filter(d => d.type === 'phase_start');

                // Clear previous drawing
                this.svg.selectAll('*').remove();

                if (!measurements.length) return;

                const xExtent = d3.extent(measurements, d => new Date(d.timestamp * 1000));
                const maxY = Math.max(
                    d3.max(measurements, d => d.temperature || 0),
                    d3.max(measurements, d => d.volume || 0),
                    100
                );

                const x = d3.scaleTime().domain(xExtent).range([0, width]);
                const y = d3.scaleLinear().domain([0, maxY * 1.1]).range([height, 0]);

                // Grid
                this.svg.append('g')
                    .attr('transform', `translate(0,${height})`)
                    .call(d3.axisBottom(x).ticks(10).tickFormat(d3.timeFormat('%H:%M:%S')))
                    .selectAll('text').style('font', '10px Outfit, sans-serif').style('fill', colors.muted);

                this.svg.append('g')
                    .call(d3.axisLeft(y))
                    .selectAll('text').style('font', '10px Outfit, sans-serif').style('fill', colors.muted);

                // X axis label
                this.svg.append('text')
                    .attr('x', width / 2).attr('y', height + 30)
                    .attr('text-anchor', 'middle')
                    .style('font', '11px Outfit, sans-serif').style('fill', colors.muted)
                    .text('Time');

                // Temperature line
                const tempLine = d3.line()
                    .x(d => x(new Date(d.timestamp * 1000)))
                    .y(d => y(d.temperature || 0))
                    .curve(d3.curveMonotoneX);

                this.svg.append('path')
                    .datum(measurements)
                    .attr('fill', 'none')
                    .attr('stroke', colors.danger)
                    .attr('stroke-width', 2)
                    .attr('d', tempLine);

                // Volume line
                const volLine = d3.line()
                    .x(d => x(new Date(d.timestamp * 1000)))
                    .y(d => y(d.volume || 0))
                    .curve(d3.curveMonotoneX);

                this.svg.append('path')
                    .datum(measurements)
                    .attr('fill', 'none')
                    .attr('stroke', colors.info)
                    .attr('stroke-width', 2)
                    .attr('d', volLine);

                // Phase markers (dashed vertical lines + labels)
                phaseStarts.sort((a, b) => a.timestamp - b.timestamp).forEach((p, idx) => {
                    const px = x(new Date(p.timestamp * 1000));

                    this.svg.append('line')
                        .attr('x1', px).attr('x2', px)
                        .attr('y1', 0).attr('y2', height)
                        .attr('stroke', colors.success)
                        .attr('stroke-width', 1)
                        .attr('stroke-dasharray', '4,4');

                    const nextX = idx < phaseStarts.length - 1
                        ? x(new Date(phaseStarts[idx + 1].timestamp * 1000))
                        : width;
                    const midX = (px + nextX) / 2;
                    const label = p.loop_index == 1 ? p.label : `${p.label} (loop ${p.loop_index})`;

                    this.svg.append('text')
                        .attr('x', midX).attr('y', height - 8)
                        .attr('text-anchor', 'middle')
                        .style('font', '10px Outfit, sans-serif')
                        .style('fill', colors.success)
                        .text(label);
                });
            }
        }
    }
</script>
@endscript