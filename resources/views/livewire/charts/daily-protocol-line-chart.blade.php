@php
    $id = 'chart-' . Str::random(10);
@endphp

<div class="h-80">
    <div id="{{ $id }}" class="w-full h-full"></div>
</div>

@script
<script>
    (function () {
        const labels = @json($labels);
        const datasets = @json($dataset);
        const container = document.getElementById("{{ $id }}");
        if (!container || !window.d3) return;

        const margin = { top: 20, right: 20, bottom: 50, left: 50 };
        const width = container.clientWidth - margin.left - margin.right;
        const height = container.clientHeight - margin.top - margin.bottom;

        const svg = d3.select(container).append('svg')
            .attr('width', '100%')
            .attr('height', '100%')
            .attr('viewBox', `0 0 ${container.clientWidth} ${container.clientHeight}`)
            .append('g')
            .attr('transform', `translate(${margin.left},${margin.top})`);

        const x = d3.scalePoint().domain(labels).range([0, width]);
        const allValues = datasets.flatMap(ds => ds.data);
        const y = d3.scaleLinear().domain([0, d3.max(allValues) * 1.1]).range([height, 0]);

        svg.append('g').attr('transform', `translate(0,${height})`).call(d3.axisBottom(x));
        svg.append('g').call(d3.axisLeft(y));

        // X axis label
        svg.append('text')
            .attr('x', width / 2).attr('y', height + 40)
            .attr('text-anchor', 'middle').attr('class', 'text-xs fill-current text-muted-foreground')
            .text('Time');

        // Y axis label
        svg.append('text')
            .attr('transform', 'rotate(-90)').attr('x', -height / 2).attr('y', -40)
            .attr('text-anchor', 'middle').attr('class', 'text-xs fill-current text-muted-foreground')
            .text('Protocol Number');

        const area = d3.area()
            .x((d, i) => x(labels[i]))
            .y0(height)
            .y1(d => y(d))
            .curve(d3.curveMonotoneX);

        const line = d3.line()
            .x((d, i) => x(labels[i]))
            .y(d => y(d))
            .curve(d3.curveMonotoneX);

        datasets.forEach(ds => {
            const color = ds.borderColor || ds.backgroundColor;
            svg.append('path')
                .datum(ds.data)
                .attr('fill', color.replace('255)', '0.1)'))
                .attr('d', area);
            svg.append('path')
                .datum(ds.data)
                .attr('fill', 'none')
                .attr('stroke', color)
                .attr('stroke-width', 2)
                .attr('d', line);
        });

        // Legend
        const legend = svg.append('g').attr('transform', `translate(0, ${height + 25})`);
        datasets.forEach((ds, i) => {
            const color = ds.borderColor || ds.backgroundColor;
            const g = legend.append('g').attr('transform', `translate(${i * 180}, 0)`);
            g.append('rect').attr('width', 12).attr('height', 12).attr('fill', color);
            g.append('text').attr('x', 16).attr('y', 10).attr('class', 'text-xs fill-current').text(ds.label);
        });
    })();
</script>
@endscript
