@php
    $id = 'chart-' . Str::random(10);
@endphp

<div>
    <div id="{{ $id }}" class="w-full" style="height: 320px;"></div>
</div>

@script
<script>
    (function () {
        const labels = @json($labels);
        const datasets = @json($dataset);
        const container = document.getElementById("{{ $id }}");
        if (!container || !window.d3) return;

        const data = datasets[0]?.data || [];
        const width = container.clientWidth;
        const height = container.clientHeight;
        const radius = Math.min(width, height - 40) / 2;

        const colors = d3.scaleOrdinal()
            .domain(labels)
            .range(['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40']);

        const svg = d3.select(container).append('svg')
            .attr('width', '100%')
            .attr('height', '100%')
            .attr('viewBox', `0 0 ${width} ${height}`);

        const g = svg.append('g')
            .attr('transform', `translate(${width / 2},${(height - 30) / 2})`);

        const pie = d3.pie().value(d => d);
        const arc = d3.arc().innerRadius(0).outerRadius(radius);

        const tooltip = d3.select(container).append('div')
            .attr('class', 'absolute bg-background border border-border rounded px-2 py-1 text-xs pointer-events-none opacity-0 transition-opacity')
            .style('position', 'absolute');

        g.selectAll('path')
            .data(pie(data))
            .join('path')
            .attr('d', arc)
            .attr('fill', (d, i) => colors(labels[i]))
            .attr('stroke', 'white')
            .attr('stroke-width', 2)
            .on('mouseenter', function (event, d) {
                tooltip.style('opacity', '1')
                    .html(`${labels[d.index]}: ${d.data} ml`);
            })
            .on('mousemove', function (event) {
                const rect = container.getBoundingClientRect();
                tooltip.style('left', (event.clientX - rect.left + 10) + 'px')
                    .style('top', (event.clientY - rect.top - 10) + 'px');
            })
            .on('mouseleave', function () {
                tooltip.style('opacity', '0');
            });

        // Legend
        const legend = svg.append('g')
            .attr('transform', `translate(${width / 2 - (labels.length * 50)}, ${height - 20})`);

        labels.forEach((label, i) => {
            const g = legend.append('g').attr('transform', `translate(${i * 100}, 0)`);
            g.append('rect').attr('width', 12).attr('height', 12).attr('fill', colors(label));
            g.append('text').attr('x', 16).attr('y', 10).attr('class', 'text-xs fill-current').text(label);
        });
    })();
</script>
@endscript
